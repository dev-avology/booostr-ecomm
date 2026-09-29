<script>
(function ($) {
    window.syncWriteInAmountBlock = function ($box) {
        var enabled = $box.find('.write-in-amount-radio:checked').val() === '1';
        var $scope = $box.closest('.write-in-amount-block');
        if (!$scope.length) {
            $scope = $box.parent();
        }
        $scope.find('.write-in-amount-yes-help').toggle(enabled);
        $scope.find('.write-in-label-fixed').toggle(!enabled);
        $scope.find('.write-in-label-min').toggle(enabled);
        $scope.find('.write-in-min-hint').toggle(enabled);
    };

    window.syncAllWriteInAmountBlocks = function () {
        $('.write-in-amount-box').each(function () {
            window.syncWriteInAmountBlock($(this));
        });
    };

    $(document).on('change', '.write-in-amount-radio', function () {
        window.syncWriteInAmountBlock($(this).closest('.write-in-amount-box'));
    });

    $(function () {
        window.syncAllWriteInAmountBlocks();
    });
})(jQuery);
</script>
