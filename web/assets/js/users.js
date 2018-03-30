$(document).ready(function () {

    var ias = jQuery.ias({
      container: '.box-users',
      item: '.user-item',
      paginatiom: '.pagination',
      next: '.pagination .next_link',
      triggerPageThreshold: 2
    });

    ias.extension(new IASTriggerExtension({
      text: 'Ver más',
      offset: 3
    }));

    ias.extension(new IASSpinnerExtension({
      src: '/web/assets/images/ajax-loader.gif'
    }));

    ias.extension(new IASNoneLeftExtension({
      text: 'No hay más personas'
    }));

});