import { md3 } from 'vuetify/blueprints'

export const vuetifyConfig = {
  blueprint: md3,
  theme: {
    defaultTheme: 'ecogreen',
    themes: {
      ecogreen: {
        dark: false,
        colors: {
          primary:             '#4CAF50',
          'primary-darken-1':  '#2E7D32',
          'primary-lighten-1': '#81C784',
          secondary:           '#607D8B',
          accent:              '#FF9800',
          error:               '#F44336',
          warning:             '#FF9800',
          info:                '#2196F3',
          success:             '#4CAF50',
          surface:             '#FFFFFF',
          background:          '#F5F5F5',
        },
      },
    },
  },
  defaults: {
    VBtn:              { rounded: 'lg', elevation: 0 },
    VCard:             { rounded: 'lg', elevation: 1 },
    VTextField:        { variant: 'outlined', density: 'compact' },
    VSelect:           { variant: 'outlined', density: 'compact' },
    VCombobox:         { variant: 'outlined', density: 'compact' },
    VAutocomplete:     { variant: 'outlined', density: 'compact' },
    VTextarea:         { variant: 'outlined', density: 'compact' },
    VDataTable:        { density: 'compact' },
    VDataTableServer:  { density: 'compact' },
  },
}
