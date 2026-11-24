<template>
  <k-field
    :help="help"
    :input="false"
    :label="label"
    :name="name"
    :type="type"
    class="kem-local-forms-field"
  >
    <k-headline class="kem-local-forms-headline">
      {{ panel.t('philippoehrlein.kirby-email-manager.panel.form_overrides.title') }}
    </k-headline>
    <k-items v-if="collection.length > 0" :items="collection" />
    <k-empty
      v-else
      :text="panel.t('philippoehrlein.kirby-email-manager.panel.form_overrides.empty')"
      icon="page"
    />
  </k-field>
</template>

<script setup lang="ts">
import { usePanel } from 'kirbyuse';

interface Icon {
  icon: string;
  back: string;
  color: string;
}

interface CollectionItem {
  id: string;
  text: string;
  link: string;
  image: Icon;
}

withDefaults(defineProps<{
  collection?: CollectionItem[];
  help?: string;
  label?: string;
  name?: string;
  type?: string;
}>(), {
  collection: () => [],
});

const panel = usePanel();
</script>

<style scoped>
.kem-local-forms-field {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-5);
}
.kem-local-forms-headline {
  font-size: var(--text-h2);
  font-weight: var(--font-normal);
}
</style>
