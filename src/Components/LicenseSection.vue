<template>
  <div v-if="!isActivated" class="license-section">
    <div class="license-section-header">
      <k-icon type="key" color="pink-600" />
      <h1>{{ panel.t('philippoehrlein.kirby-email-manager.panel.license.headline') }}</h1>
    </div>
    <buy-activate-buttons @activated="setActivated" />
  </div>
</template>

<script setup lang="ts">
import { ref, usePanel } from 'kirbyuse';

const panel = usePanel();
const isActivated = ref<boolean>(false);

const setActivated = (activated: boolean) => {
  isActivated.value = activated;
};
</script>

<style scoped>
.license-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  border: 1px solid var(--color-pink-300);
  background-color: var(--color-pink-100);
  border-radius: var(--rounded);
  padding: var(--spacing-4);
  margin-bottom: var(--spacing-4);
  
  @media (prefers-color-scheme: dark) {
    background-color: var(--color-pink-900);
    border-color: var(--color-pink-800);
  }

  @media (min-width: 768px) {
    flex-direction: row;
  }
}

.license-section-header {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  font-size: var(--text-md);
  font-weight: var(--font-bold);
  color: var(--color-pink-600);
  @media (prefers-color-scheme: dark) {
    color: var(--color-pink-400);
    background-color: var(--color-pink-900);
    border-color: var(--color-pink-800);
  }
}

</style>