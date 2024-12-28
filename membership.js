$(document).ready(function() {
    $('.upgrade-button').on('click', function() {
        var membershipId = $(this).data('membership-id'); 
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { action: 'upgrade', membership_id: membershipId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.current-plan').html(`
                        <h3>Your Current Membership Plan:</h3>
                        <h2><span>${response.current_membership.plan_name}</span></h2>
                        <p>Price: <span>LKR ${response.current_membership.price}</span></p>
                        <ul>
                            ${response.current_membership.benefits.split(',').map(benefit => `<li>${benefit.trim()}</li>`).join('')}
                        </ul>
                    `);
                    alert(response.message); 
                } else {
                    alert(response.message); 
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while upgrading the membership. Please try again.');
            }
        });
    });

    $(document).on('click', '#renew-button', function() {
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { action: 'renew' },
            dataType: 'json',
            success: function(response) {
                alert(response.message); 
            },
            error: function(xhr, status, error) {
                alert('An error occurred while renewing the membership. Please try again.');
            }
        });
    });

    $(document).on('click', '#cancel-button', function() {
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { action: 'cancel' },
            dataType: 'json',
            success: function(response) {
                alert(response.message); 
            },
            error: function(xhr, status, error) {
                alert('An error occurred while cancelling the membership. Please try again.');
            }
        });
    });
});
