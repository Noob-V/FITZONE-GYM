$(document).ready(function () {
    $('.upgrade-button').click(function () {
        var membershipId = $(this).data('membership-id');

        $.ajax({
            type: 'POST',
            url: 'membership_overview.php', 
            data: { membership_id: membershipId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    
                    $('.current-plan h2 span').text(response.current_membership.plan_name);
                    $('.current-plan p span').text('LKR ' + response.current_membership.price);
                    $('.current-plan ul').empty(); 
                    response.current_membership.benefits.split(',').forEach(function (benefit) {
                        $('.current-plan ul').append('<li>' + benefit.trim() + '</li>');
                    });
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText); 
                alert('An error occurred while upgrading the membership.');
            }
        });
    });
});