# SKILL: Systematic Debugging

## When to use
When facing an unexpected error, failing test, or broken behaviour.

## Steps
1. **Reproduce** — confirm the bug with the smallest possible case
2. **Isolate** — narrow: feature test → unit test → single method → single line
3. **Hypothesise** — state what you believe is wrong and why
4. **Verify** — test the hypothesis before applying any fix
5. **Fix** — apply the minimal change that resolves the root cause
6. **Confirm** — re-run tests; check for regressions

## Useful commands
```bash
php artisan test --filter=TestName      # run one test
php artisan test --filter=ClassName     # run one test class
php artisan route:list --path=api       # verify routes exist
php artisan config:clear                # clear config cache
tail -f storage/logs/laravel.log        # live log
```

## Anti-patterns
- Do not `dd()` in production code paths
- Do not comment out failing tests — fix or skip with a reason
- Do not apply random fixes without a hypothesis
