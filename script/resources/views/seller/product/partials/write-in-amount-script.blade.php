<script>
(function ($) {
    function writeInPriceInput($scope) {
        return $scope.find('.write-in-price-wrap input.product_price_input, .write-in-price-wrap input[name="price"], .write-in-price-wrap input[name$="[price]"]').first();
    }

    function formatWriteInZero($input) {
        if ($input.hasClass('product_price_input')) {
            return '$0.00';
        }
        return '0.00';
    }

    window.syncWriteInAmountBlock = function ($box, fromUser) {
        var enabled = $box.find('.write-in-amount-radio:checked').val() === '1';
        var $scope = $box.closest('.write-in-amount-block');
        if (!$scope.length) {
            $scope = $box.parent();
        }
        if (typeof $scope.data('writeInSavedEnabled') === 'undefined') {
            $scope.data('writeInSavedEnabled', $box.find('.write-in-amount-radio[value="1"]').is(':checked') ? 1 : 0);
        }
        $scope.find('.write-in-amount-yes-help').toggle(enabled);
        $scope.find('.write-in-label-fixed').toggle(!enabled);
        $scope.find('.write-in-label-min').toggle(enabled);
        $scope.find('.write-in-min-hint').toggle(enabled);

        if (!fromUser) {
            return;
        }

        var $price = writeInPriceInput($scope);
        if (!$price.length) {
            return;
        }

        if (enabled) {
            if ($scope.data('writeInSavedEnabled') === 1) {
                return;
            }
            if (typeof $scope.data('writeInOriginalPrice') === 'undefined') {
                $scope.data('writeInOriginalPrice', $price.val());
            }
            $price.val(formatWriteInZero($price));
            return;
        }

        if (typeof $scope.data('writeInOriginalPrice') !== 'undefined') {
            $price.val($scope.data('writeInOriginalPrice'));
        }
    };

    window.syncAllWriteInAmountBlocks = function () {
        $('.write-in-amount-box').each(function () {
            window.syncWriteInAmountBlock($(this), false);
        });
    };

    $(document).on('change', '.write-in-amount-radio', function () {
        window.syncWriteInAmountBlock($(this).closest('.write-in-amount-box'), true);
    });

    $(function () {
        window.syncAllWriteInAmountBlocks();
    });
})(jQuery);
</script>
