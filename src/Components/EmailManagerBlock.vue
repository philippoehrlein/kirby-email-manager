<template>
  <div>
    <k-block-title :fieldset="{
      'icon': 'email-manager',
      'name': 'Email Manager'
    }" />
    <ul class="email-status-list">
      <li v-for="(value, key) in statusItems" :key="key">
        <k-icon 
          :type="validationRules[key] ? 'check' : 'cancel'" 
          :color="validationRules[key] ? 'positive' : 'negative'" 
        />{{ value }}
      </li>
      <li v-if="content.gdpr_checkbox">
        <k-icon 
          :type="validationRules.gdpr ? 'check' : 'cancel'" 
          :color="validationRules.gdpr ? 'positive' : 'negative'" 
        />{{ $t('philippoehrlein.kirby-email-manager.panel.status.gdpr') }}
      </li>
    </ul>
  </div>

</template>

<script>
export default {
  computed: {
    isSingleEmail() {
      return !this.content.send_to_more;
    },

    hasEmailTemplate() {
      return !!this.content.email_templates;
    },

    validationRules() {
      return {
        emailTemplate: !!this.content.email_templates,
        recipients: this.validateRecipients(),
        successMessage: this.validateSuccessMessage(),
        gdpr: this.validateGdpr()
      };
    },

    allRequiredFieldsSet() {
      return Object.values(this.validationRules).every(value => value);
    },

    statusItems() {
      return {
        emailTemplate: this.$t('philippoehrlein.kirby-email-manager.panel.status.email_template'),
        recipients: this.$t('philippoehrlein.kirby-email-manager.panel.status.recipients'),
        successMessage: this.$t('philippoehrlein.kirby-email-manager.panel.status.success_message')
      };
    }
  },

  methods: {
    validateRecipients() {
      return this.isSingleEmail 
        ? !!this.content.send_to
        : this.content.send_to_structure?.length > 0;
    },

    validateSuccessMessage() {
      return this.isSingleEmail
        ? !!(this.content.send_to_success_title && this.content.send_to_success_text)
        : this.content.send_to_structure?.every(item => item.success_title && item.success_text);
    },

    validateGdpr() {
      if (!this.content.gdpr_checkbox) return true;
      return this.content.gdpr_text.length > 0;
    }
  },
  created() {
    console.log(this.$store._actions);
  }
}
</script>
<style scoped>
.email-status-list {
  margin: var(--spacing-2) 0;
}

.email-status-list li {
  display: flex;
  color: var(--color-gray-800);
  flex-direction: row;
  align-items: center;
  gap: var(--spacing-2);
  padding: var(--spacing-1) 0;
}

</style>
