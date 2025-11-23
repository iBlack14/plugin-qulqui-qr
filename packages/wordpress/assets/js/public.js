/**
 * Culqi QR Public Scripts
 */

(function($) {
    'use strict';

    // Generate QR on button click
    $('.culqi-qr-button').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var amount = button.data('amount');
        var currency = button.data('currency');
        var description = button.data('description');
        var successUrl = button.data('success-url');
        var cancelUrl = button.data('cancel-url');

        var modal = button.closest('.culqi-qr-wrapper').find('.culqi-qr-modal');
        var modalContent = modal.find('.culqi-qr-content');
        var loading = modal.find('.culqi-qr-loading');

        // Show modal
        modal.show();
        loading.show();
        modalContent.hide();

        // Generate QR
        $.ajax({
            url: culqiQRData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'culqi_qr_generate',
                nonce: culqiQRData.nonce,
                amount: amount,
                currency: currency,
                description: description
            },
            success: function(response) {
                loading.hide();

                if (response.success) {
                    var qr = response.data;

                    var html = '<div class="culqi-qr-display">';
                    html += '<h3>Escanea el código QR</h3>';
                    html += '<img src="' + qr.qr_code + '" alt="QR Code" class="qr-image" />';
                    html += '<div class="qr-info">';
                    html += '<p><strong>Monto:</strong> ' + qr.currency + ' ' + qr.amount.toFixed(2) + '</p>';
                    html += '<p><strong>ID:</strong> <code>' + qr.payment_id + '</code></p>';
                    html += '<p class="status">Estado: <span class="status-' + qr.status + '">' + qr.status + '</span></p>';
                    html += '</div>';
                    html += '<button class="button check-status" data-payment-id="' + qr.payment_id + '">Verificar Pago</button>';
                    html += '</div>';

                    modalContent.html(html).show();

                    // Auto-check status every 5 seconds
                    startStatusCheck(qr.payment_id, successUrl);
                } else {
                    modalContent.html('<p class="error">Error: ' + response.data + '</p>').show();
                }
            },
            error: function() {
                loading.hide();
                modalContent.html('<p class="error">Error al generar QR. Por favor intenta de nuevo.</p>').show();
            }
        });
    });

    // Close modal
    $(document).on('click', '.culqi-qr-close', function() {
        $(this).closest('.culqi-qr-modal').hide();
    });

    // Check status manually
    $(document).on('click', '.check-status', function() {
        var button = $(this);
        var paymentId = button.data('payment-id');

        checkPaymentStatus(paymentId, function(status) {
            if (status === 'completed') {
                alert('¡Pago completado exitosamente!');
                location.reload();
            } else {
                alert('Pago aún pendiente');
            }
        });
    });

    // Form submission
    $('.culqi-qr-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var amount = form.find('[name="amount"]').val();
        var currency = form.find('[name="currency"]').val();
        var description = form.find('[name="description"]').val();
        var result = form.find('.culqi-qr-result');

        result.html('<p>Generando QR...</p>').show();

        $.ajax({
            url: culqiQRData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'culqi_qr_generate',
                nonce: culqiQRData.nonce,
                amount: amount,
                currency: currency,
                description: description
            },
            success: function(response) {
                if (response.success) {
                    var qr = response.data;

                    var html = '<div class="culqi-qr-display">';
                    html += '<h3>Escanea el código QR</h3>';
                    html += '<img src="' + qr.qr_code + '" alt="QR Code" class="qr-image" />';
                    html += '<div class="qr-info">';
                    html += '<p><strong>Monto:</strong> ' + qr.currency + ' ' + qr.amount.toFixed(2) + '</p>';
                    html += '<p><strong>ID:</strong> <code>' + qr.payment_id + '</code></p>';
                    html += '</div>';
                    html += '</div>';

                    result.html(html);
                } else {
                    result.html('<p class="error">Error: ' + response.data + '</p>');
                }
            },
            error: function() {
                result.html('<p class="error">Error al generar QR</p>');
            }
        });
    });

    // Auto status check
    function startStatusCheck(paymentId, successUrl) {
        var interval = setInterval(function() {
            checkPaymentStatus(paymentId, function(status) {
                if (status === 'completed') {
                    clearInterval(interval);
                    alert('¡Pago completado!');

                    if (successUrl) {
                        window.location.href = successUrl;
                    } else {
                        location.reload();
                    }
                }
            });
        }, 5000); // Check every 5 seconds
    }

    // Check payment status
    function checkPaymentStatus(paymentId, callback) {
        $.ajax({
            url: culqiQRData.restUrl + 'culqi-qr/v1/check-status',
            method: 'POST',
            data: {
                payment_id: paymentId
            },
            success: function(response) {
                if (response.status) {
                    callback(response.status);
                }
            }
        });
    }

})(jQuery);
