<template>
  <k-button-group v-if="!activated" layout="collapsed">
    <k-button variant="filled" icon="cart" theme="pink" size="sm" :link="buyurl">
      {{ panel.t('philippoehrlein.kirby-email-manager.activate-section.button.buy') }}
    </k-button>
    <k-button variant="filled" icon="key" theme="pink" size="sm" dialog="email-manager/license/activation">
      {{ panel.t('activate') }}
    </k-button>
  </k-button-group>
</template>

<script setup lang="ts">
import { usePanel } from 'kirbyuse';
import { onMounted, ref } from 'vue';

const emit = defineEmits(['activated']);
const panel = usePanel();
const activated = ref<boolean>(false);

const buyurl = 'https://email-manager.philippoehrlein.de#buy';

onMounted(async () => {
  // check if license is activated
  try {
    const res: any = await panel.api.get('email-manager/license/status');
    activated.value = !!res?.activated;
    emit('activated', activated.value);
  } catch (e) {
    console.error(e);
    activated.value = false;
  }

  // listen for license activation events
  panel.events.on('license.activated', () => {
    activated.value = true;
    emit('activated', activated.value);
  });
});
</script>
