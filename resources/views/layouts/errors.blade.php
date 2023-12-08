@if(!$user->is_active() && $user->is_carrier_set())
<div class="alert alert-info ">
    <strong>Heads up!</strong> Please validate your profile. <a href="/profile/sendcode">Click here</a> to send validation code.
</div>
@elseif(!$user->is_active() && !$user->is_carrier_set())
<div class="alert alert-info ">
    <strong>Heads up!</strong> Please update your <a href="/profile/view">profile</a> with SMS Carrier/Number information to get dining alerts.
</div>
@elseif(!$user->is_carrier_set())
<div class="alert alert-info ">
    <strong>Heads up!</strong> Please update your <a href="/profile/view">profile</a> with SMS Carrier Name information to get dining alerts.
</div>
@endif