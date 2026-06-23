import { api } from '@/plugins/axios'

// Keycloak auth — Authorization Code + PKCE in the SPA, then the Keycloak
// access_token is exchanged for a Sanctum token at the backend.
//
// Required frontend env vars (frontend/.env):
//   VITE_KEYCLOAK_URL=https://keycloak.example.com
//   VITE_KEYCLOAK_REALM=my-realm
//   VITE_KEYCLOAK_CLIENT_ID=my-spa-client
//   VITE_KEYCLOAK_REDIRECT_URI=http://localhost:5173/login   (optional)

const cfg = {
  url:         import.meta.env.VITE_KEYCLOAK_URL,
  realm:       import.meta.env.VITE_KEYCLOAK_REALM,
  clientId:    import.meta.env.VITE_KEYCLOAK_CLIENT_ID,
  redirectUri: import.meta.env.VITE_KEYCLOAK_REDIRECT_URI || `${window.location.origin}/login`,
}

const base = () => `${cfg.url}/realms/${cfg.realm}/protocol/openid-connect`

function randomString(len = 64): string {
  const bytes = new Uint8Array(len)
  crypto.getRandomValues(bytes)
  return Array.from(bytes, b => ('0' + (b & 0xff).toString(16)).slice(-2)).join('')
}

function base64url(buffer: ArrayBuffer): string {
  return btoa(String.fromCharCode(...new Uint8Array(buffer)))
    .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '')
}

async function pkceChallenge(verifier: string): Promise<string> {
  const digest = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(verifier))
  return base64url(digest)
}

export default {
  // Step 1 — redirect the browser to Keycloak's login page.
  async beginLogin() {
    const verifier  = randomString()
    const state     = randomString(16)
    const challenge = await pkceChallenge(verifier)

    sessionStorage.setItem('kc_verifier', verifier)
    sessionStorage.setItem('kc_state', state)

    const params = new URLSearchParams({
      client_id:             cfg.clientId,
      redirect_uri:          cfg.redirectUri,
      response_type:         'code',
      scope:                 'openid profile email',
      state,
      code_challenge:        challenge,
      code_challenge_method: 'S256',
    })

    window.location.href = `${base()}/auth?${params.toString()}`
  },

  // Step 2 — exchange the returned ?code for a Keycloak access_token.
  async exchangeCode(code: string, state: string | null) {
    const verifier      = sessionStorage.getItem('kc_verifier')
    const expectedState = sessionStorage.getItem('kc_state')

    if (!verifier || state !== expectedState) {
      throw new Error('Invalid PKCE state — please retry login.')
    }

    const body = new URLSearchParams({
      grant_type:    'authorization_code',
      client_id:     cfg.clientId,
      code,
      redirect_uri:  cfg.redirectUri,
      code_verifier: verifier,
    })

    const res = await fetch(`${base()}/token`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body,
    })

    if (!res.ok) throw new Error('Token exchange failed.')

    sessionStorage.removeItem('kc_verifier')
    sessionStorage.removeItem('kc_state')

    const json = await res.json()
    return json.access_token as string
  },

  // Step 3 — hand the Keycloak access_token to the backend for a Sanctum token.
  loginWithToken: (accessToken: string) => api.post('/auth/login', { access_token: accessToken }),

  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
}
