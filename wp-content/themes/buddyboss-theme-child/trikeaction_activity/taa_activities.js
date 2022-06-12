jQuery(document).ready(function() {
    console.log('loaded');
    jQuery("#ordertrackingsubmit").on("click", function (e) {
        var quotenum = jQuery("#quotenum").val();
        console.log(quotenum);
    });
});
