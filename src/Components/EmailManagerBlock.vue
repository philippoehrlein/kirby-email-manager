<template>
  <div>
    <header class="email-manager-header">
      <h1><svg aria-hidden="true" data-type="email-manager" class="k-icon"><use xlink:href="#icon-email-manager"></use></svg>Email Manager</h1>
    </header>
    <div class="email-status-list" v-if="allRequiredFieldsSet">
      <k-box theme="positive" icon="check">
        <p>{{ $t('panel.status.all_fields_set') }}</p>
      </k-box>
    </div>
    <div class="email-status-list" v-else>
      <k-box theme="error" icon="alert">
        <p>{{ $t('panel.status.not_all_fields_set') }}</p>
      </k-box>
    </div>
  </div>

</template>

<script>
export default {
  computed: {
    allRequiredFieldsSet() {
      const is_single_email = !this.content.send_to_more;
      let all_fields_set = false;
      
      if (is_single_email) {
        all_fields_set = this.content.email_templates && 
                        this.content.send_to &&
                        this.content.send_to_success_title &&
                        this.content.send_to_success_text;
      } else {
        all_fields_set = this.content.email_templates && 
                        this.content.send_to_structure &&
                        this.content.send_to_structure.length > 0;
      }

      if (this.content.gdpr_checkbox && !this.content.gdpr_text) {
        all_fields_set = false;
      }
      
      return all_fields_set;
    }
  }
}
</script>
<style scoped>
.email-manager-header {
  margin-bottom: var(--spacing-4);
}
.email-manager-header h1 {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: var(--spacing-3);
}

.email-manager-header svg {
  color: var(--color-gray-600);
}
</style>
