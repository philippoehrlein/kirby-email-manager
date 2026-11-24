<template>
  <div class="k-block">
    <k-block-title 
      :fieldset="{
        icon: 'email-manager',
        name: 'Email Manager',
        label: selectedEmailTemplate ?? false
      }" 
    />
    <ul class="email-status-list">
      <li
        v-for="(value, key) in statusItems"
        :key="key"
        :data-theme="validationRules[key] ? 'positive' : 'negative'"
      >
        <k-icon
          :type="validationRules[key] ? 'check' : 'cancel'"
          class="email-status-icon"
        />
        {{ value }}
      </li>
      <li
        v-if="content.gdpr_checkbox"
        :data-theme="isGdprValid ? 'positive' : 'negative'"
      >
        <k-icon
          :type="isGdprValid ? 'check' : 'cancel'"
          class="email-status-icon"
        />
        {{ panel.t('philippoehrlein.kirby-email-manager.panel.status.gdpr') }}
      </li>
      <li
        v-if="content.kem_email_legal_form"
        data-theme="info"
      >
        <k-icon
          type="info"
          class="email-status-icon"
        />
        {{ panel.t('philippoehrlein.kirby-email-manager.panel.status.local_legal_forms') }}
      </li>
    </ul>
    <email-manager-buy-activate-buttons />
  </div>
</template>

<script setup lang="ts">
import { usePanel } from 'kirbyuse';
import { computed } from 'vue';


// Types
interface EmailTemplateOption {
  value: string;
  text: string;
}

interface EmailTab {
  fields: {
    email_templates: {
      options: EmailTemplateOption[];
    };
  };
}

interface Tabs {
  email: EmailTab;
}

interface EmailManagerContent {
  email_templates: string;
  send_to_more: boolean;
  send_to: string;
  send_to_success_title: string;
  send_to_success_text: string;
  kem_email_legal_form: boolean;
  gdpr_checkbox: boolean;
  gdpr_text: string;
  email_legal_footer: string;
  send_to_structure: {
    topic: string;
    email: string;
  }[];
}

interface ValidationRules {
  emailTemplate: boolean;
  recipients: boolean;
  successMessage: boolean;
  gdpr: boolean;
}

interface StatusItems {
  emailTemplate: string;
  recipients: string;
  successMessage: string;
}

// Props
const props = defineProps<{
  content: EmailManagerContent;
  tabs: Tabs;
}>();

// Composables
const panel = usePanel();

// Computed properties
const computedContent = computed<EmailManagerContent>(() => props.content);
const tabs = computed<Tabs>(() => props.tabs);

const selectedEmailTemplate = computed(() => {
  const emailTemplate = tabs.value.email.fields.email_templates.options.find(
    option => option.value === computedContent.value.email_templates
  );
  return emailTemplate ? emailTemplate.text : false;
});

const isSingleEmail = computed(() => !computedContent.value.send_to_more);

// Validation functions
const validateRecipients = (): boolean => {
  return isSingleEmail.value 
    ? !!computedContent.value.send_to
    : (computedContent.value.send_to_structure?.length ?? 0) > 0;
};

const validateSuccessMessage = (): boolean => {
  return !!(computedContent.value.send_to_success_title && computedContent.value.send_to_success_text);
};

const validateGdpr = (): boolean => {
  if (!computedContent.value.gdpr_checkbox) return true;
  return computedContent.value.gdpr_text.length > 0;
};

const validationRules = computed(() => ({
  emailTemplate: !!computedContent.value.email_templates,
  recipients: validateRecipients(),
  successMessage: validateSuccessMessage(),
  gdpr: validateGdpr()
} as ValidationRules));

const isGdprValid = computed(() => validateGdpr());

const statusItems = computed<StatusItems>(() => ({
  emailTemplate: panel.t('philippoehrlein.kirby-email-manager.panel.status.email_template'),
  recipients: panel.t('philippoehrlein.kirby-email-manager.panel.status.recipients'),
  successMessage: panel.t('philippoehrlein.kirby-email-manager.panel.status.success_message')
}));
</script>
<style scoped>
.email-status-list {
  margin: var(--spacing-2) 0;
}

.email-status-list li {
  display: flex;
  color: var(--color-text-dimmed);
  flex-direction: row;
  align-items: center;
  gap: var(--spacing-2);
  padding: var(--spacing-1) 0;
}

.email-status-icon {
  color: light-dark(var(--theme-color-700), var(--theme-color-400));
}
</style>