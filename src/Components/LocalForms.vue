<template>
  <div class="kem-local-forms-container">
    <k-headline class="kem-local-forms-headline">
      {{ panel.t('philippoehrlein.kirby-email-manager.panel.form_overrides.title') }}
    </k-headline>
    <k-section :label="panel.t('philippoehrlein.kirby-email-manager.panel.form_overrides.label')">
      <k-items v-if="collection.length > 0" :items="collection" />
      <k-empty v-else :text="panel.t('philippoehrlein.kirby-email-manager.panel.form_overrides.empty')" icon="page" />
    </k-section>
  </div>
</template>

<script setup lang="ts">
import { usePanel } from 'kirbyuse';

interface Icon {
  icon: string;
  back: string;
  color: string;
}

interface Collection {
  id: string;
  text: string;
  link: string;
  image: Icon;
}

defineProps<{
  collection: Collection[];
}>();
const panel = usePanel();
</script>

<style scoped>
.kem-local-forms-container {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-5);
}
.kem-local-forms-headline {
  font-size: var(--text-h2);
  font-weight: var(--font-normal);
}
</style>