#!/usr/bin/env bash
# dev-agent.sh — Claude Code session launcher for EO-ADS projects
#
# Automatically injects the 3-layer context (CLAUDE.md, ARCHITECTURE.md,
# active sprint doc) so every session starts with full project awareness.
#
# Usage:
#   ./dev-agent.sh                         # auto-detect latest sprint
#   ./dev-agent.sh sprint-02               # load a specific sprint
#   ./dev-agent.sh sprint-02 "add module"  # load sprint + opening prompt

set -euo pipefail

SPRINT="${1:-}"
PROMPT="${2:-}"

# Auto-detect the latest non-archived sprint
if [ -z "$SPRINT" ]; then
  SPRINT=$(ls .docs/sprints/sprint-*.md 2>/dev/null \
    | grep -v archive \
    | sort \
    | tail -1 \
    | xargs -I{} basename {} .md)
fi

SPRINT_FILE=".docs/sprints/${SPRINT}.md"

if [ ! -f "$SPRINT_FILE" ]; then
  echo "❌  Sprint file not found: $SPRINT_FILE"
  echo "    Available sprints:"
  ls .docs/sprints/sprint-*.md 2>/dev/null | grep -v archive | xargs -I{} basename {} .md | sed 's/^/      /'
  exit 1
fi

echo "📋  Sprint: $SPRINT"
echo "🤖  Starting Claude Code with full project context..."
echo ""

CONTEXT_FILES=(
  ".claude/CLAUDE.md"
  ".docs/ARCHITECTURE.md"
  "$SPRINT_FILE"
)

CONTEXT_ARGS=""
for f in "${CONTEXT_FILES[@]}"; do
  [ -f "$f" ] && CONTEXT_ARGS="$CONTEXT_ARGS --context $f"
done

if [ -n "$PROMPT" ]; then
  claude $CONTEXT_ARGS --message "$PROMPT"
else
  claude $CONTEXT_ARGS
fi
