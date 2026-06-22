# SKILL: Test-Driven Development

## When to use
Before implementing any Action, Service, Repository, or API endpoint.

## Steps
1. **Red** — write a failing test describing the desired behaviour
2. **Green** — write the minimum code to make it pass
3. **Refactor** — clean up; tests must stay green

## Patterns

```php
// Feature test (HTTP layer) — tests/Feature/
it('creates a purchase order and returns 201', function () {
    $user = User::factory()->create();
    $payload = PurchaseOrder::factory()->make()->toArray();

    $this->actingAs($user)
         ->postJson('/api/purchase-order', $payload)
         ->assertCreated()
         ->assertJsonStructure(['data' => ['id'], 'message']);
});

// Unit test (Action/Service layer) — tests/Unit/
it('calculates order total correctly', function () {
    $result = (new CalculateOrderTotalAction)->execute([
        ['qty' => 2, 'price' => 50000],
    ]);
    expect($result)->toBe(100000);
});
```

## Checklist
- [ ] Happy path covered by a feature test
- [ ] Validation errors tested (422 response)
- [ ] Auth guard tested (401 when unauthenticated)
- [ ] Edge cases have unit tests
