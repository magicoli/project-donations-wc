(function($) {
  $(document).ready(function() {
    var linkProjectCheckbox = $('#_linkproject');
    var donationsTab = $('#woocommerce-product-data li.donations_tab');
    var generalTab = $('#woocommerce-product-data li.general_options');

    // Hide/show the "Project Donations" tab initially based on the checkbox state
    toggleDonationsTab();

    // Toggle the "Project Donations" tab when the checkbox is clicked
    linkProjectCheckbox.on('change', function() {
      toggleDonationsTab();
    });

    // Function to toggle the "Project Donations" tab
    function toggleDonationsTab() {
      if (linkProjectCheckbox.is(':checked')) {
        donationsTab.show();
      } else {
        if (donationsTab.hasClass('active')) {
          generalTab.find('a').trigger('click');
        }
        donationsTab.hide();
      }
    }
  });
})(jQuery);
