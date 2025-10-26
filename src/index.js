import BuyActivateButtons from './Components/BuyActivateButtons.vue';
import EmailManagerBlock from './Components/EmailManagerBlock.vue';
import LicenseSection from './Components/LicenseSection.vue';

window.panel.plugin('philippoehrlein/kirby-email-manager', {
  icons: {
    'email-manager': '<path fill-rule="evenodd" clip-rule="evenodd" d="M12 0L0 6V18L12 24L24 18V6L12 0ZM12.0002 2.73176L2.73187 7.3659V16.6342L12.0002 21.2683L21.2685 16.6342V7.3659L12.0002 2.73176ZM12.0002 4.80421L5.94995 7.82932L12.0002 10.8544L18.0504 7.82932L12.0002 4.80421ZM19.4148 15.4886V9.21956L12.0002 12.9269L4.58553 9.21956V15.4886L12.0002 19.1959L19.4148 15.4886Z" fill="currentColor"/>'
  },
  blocks: {
    'email-manager': EmailManagerBlock
  },
  components: {
    'buy-activate-buttons': BuyActivateButtons
  },
  sections: {
    'email-manager-license': LicenseSection
  }
});
