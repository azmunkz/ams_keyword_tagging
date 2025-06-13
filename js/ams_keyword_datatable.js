(function ($, Drupal) {
  Drupal.behaviors.amsKeywordDataTable = {
    attach: function (context, settings) {
      if ($('#ams-keywords-table').length && !$('#ams-keywords-table').hasClass('datatable-applied')) {
        $('#ams-keywords-table').DataTable();
        $('#ams-keywords-table').addClass('datatable-applied');
      }
    }
  };
})(jQuery, Drupal);
