# Design System — Vuetify 3 + Ecogreen Theme

## Vuetify Plugin Configuration

```js
// src/plugins/vuetify.js
import { createVuetify } from 'vuetify'
import { md3 } from 'vuetify/blueprints'

export default createVuetify({
  blueprint: md3,
  theme: {
    defaultTheme: 'ecogreen',
    themes: {
      ecogreen: {
        dark: false,
        colors: {
          primary:           '#4CAF50',
          'primary-darken-1':'#2E7D32',
          'primary-lighten-1':'#81C784',
          secondary:         '#607D8B',
          accent:            '#FF9800',
          error:             '#F44336',
          warning:           '#FF9800',
          info:              '#2196F3',
          success:           '#4CAF50',
          surface:           '#FFFFFF',
          background:        '#F5F5F5',
        },
      },
    },
  },
  defaults: {
    VBtn:           { rounded: 'lg', elevation: 0 },
    VCard:          { rounded: 'lg', elevation: 1 },
    VTextField:     { variant: 'outlined', density: 'compact' },
    VSelect:        { variant: 'outlined', density: 'compact' },
    VCombobox:      { variant: 'outlined', density: 'compact' },
    VAutocomplete:  { variant: 'outlined', density: 'compact' },
    VTextarea:      { variant: 'outlined', density: 'compact' },
    VDataTable:     { density: 'compact' },
    VDataTableServer:{ density: 'compact' },
  },
})
```

---

## Component Patterns

### Stat / KPI Card
```vue
<v-card rounded="lg" elevation="1" class="pa-4">
  <div class="text-caption text-medium-emphasis text-uppercase">{{ label }}</div>
  <div class="text-h5 font-weight-bold text-primary mt-1">{{ value }}</div>
  <div class="text-caption text-success mt-1">↑ {{ trend }}</div>
</v-card>
```

### Page Header
```vue
<div class="d-flex align-center justify-space-between mb-4">
  <h1 class="text-h5 font-weight-bold">{{ title }}</h1>
  <v-btn color="primary" prepend-icon="mdi-plus" @click="onCreate">
    Add New
  </v-btn>
</div>
```

### Data Table with Actions
```vue
<v-card rounded="lg" elevation="1">
  <v-data-table-server
    :headers="headers"
    :items="store.items"
    :items-length="store.meta.total"
    :loading="store.loading"
    density="compact"
    @update:options="store.fetchAll"
  >
    <template #item.actions="{ item }">
      <v-btn icon size="x-small" variant="text" @click="edit(item)">
        <v-icon>mdi-pencil</v-icon>
      </v-btn>
      <v-btn icon size="x-small" variant="text" color="error" @click="remove(item)">
        <v-icon>mdi-delete</v-icon>
      </v-btn>
    </template>
  </v-data-table-server>
</v-card>
```

### Confirm Dialog
```vue
<v-dialog v-model="dialog" max-width="400" persistent>
  <v-card rounded="lg">
    <v-card-title class="text-h6">Confirm Delete</v-card-title>
    <v-card-text>This action cannot be undone. Continue?</v-card-text>
    <v-card-actions>
      <v-spacer />
      <v-btn variant="text" @click="dialog = false">Cancel</v-btn>
      <v-btn color="error" variant="flat" :loading="loading" @click="confirm">
        Delete
      </v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
```

### Form Dialog
```vue
<v-dialog v-model="dialog" max-width="600" persistent>
  <v-card rounded="lg">
    <v-card-title>{{ isEdit ? 'Edit' : 'Create' }} Item</v-card-title>
    <v-card-text>
      <v-form ref="formRef" @submit.prevent="submit">
        <v-text-field v-model="form.name" label="Name" :rules="[required]" />
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-spacer />
      <v-btn variant="text" @click="dialog = false">Cancel</v-btn>
      <v-btn color="primary" variant="flat" :loading="loading" @click="submit">
        Save
      </v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
```
