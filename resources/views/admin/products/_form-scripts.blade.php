<script>
    $(function() {
        // --- Image preview --------------------------------------------------
        $('#image-input').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#image-placeholder').addClass('hidden');
                $('#image-preview').attr('src', ev.target.result).removeClass('hidden');
            };
            reader.readAsDataURL(file);
        });

        // --- Live margin indicator + below-cost warning ----------------------
        function recalcMargin() {
            const cost = parseFloat($('#cost_price').val()) || 0;
            const price = parseFloat($('#selling_price').val()) || 0;
            const $indicator = $('#margin-indicator');
            const $warning = $('#below-cost-warning');

            if (cost === 0 && price === 0) {
                $indicator.addClass('hidden');
                $warning.addClass('hidden');
                return;
            }

            const margin = price - cost;
            const marginPct = cost > 0 ? ((margin / cost) * 100).toFixed(1) : '—';

            if (margin < 0) {
                $indicator
                    .removeClass('hidden bg-emerald-50 text-emerald-700')
                    .addClass('bg-red-50 text-red-700')
                    .text(`Margin: ${margin.toFixed(2)} (selling below cost)`);
                $warning.removeClass('hidden');
            } else {
                $indicator
                    .removeClass('hidden bg-red-50 text-red-700')
                    .addClass('bg-emerald-50 text-emerald-700')
                    .text(`Margin: ${margin.toFixed(2)} (${marginPct}%)`);
                $warning.addClass('hidden');
                $warning.find('input[type=checkbox]').prop('checked', false);
            }
        }

        $('#cost_price, #selling_price').on('input', recalcMargin);
        recalcMargin(); // run once on load for edit forms with pre-filled values

        // --- Barcode field: numeric-friendly, strips whitespace on blur ------
        $('#barcode-input').on('blur', function() {
            $(this).val($(this).val().trim());
        });
    });
</script>
