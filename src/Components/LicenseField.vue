<template>
  <k-field
    v-if="!isActivated"
    :help="help"
    :input="false"
    :label="label"
    :name="name"
    :type="type"
    class="license-field"
  >
    <div class="license-field-content">
      <div class="license-field-header">
        <k-icon type="key" color="pink-600" />
        <h1>{{ panel.t('philippoehrlein.kirby-email-manager.panel.license.headline') }}</h1>
      </div>
      <email-manager-buy-activate-buttons @activated="setActivated" />
    </div>
  </k-field>
</template>

<script setup lang="ts">
import { usePanel } from 'kirbyuse';
import { ref } from 'vue';

defineProps<{
  help?: string;
  label?: string;
  name?: string;
  type?: string;
}>();

const panel = usePanel();
const isActivated = ref<boolean>(false);

const setActivated = async (activated: boolean) => {
  isActivated.value = activated;

  // Reload so the PHP field returns hidden:true and the layout gap disappears
  if (activated) {
    await panel.view.reload();
  }
};
</script>

<style scoped>
.license-field-content {
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

.license-field-header {
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
  }
}
</style>
