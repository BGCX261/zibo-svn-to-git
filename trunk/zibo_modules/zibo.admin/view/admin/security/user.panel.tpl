<div id="userPanel">
{if $user}
    <span><a href="{$urlProfile}" class="profile">{$user->getUsername()}</a></span>
    |
    <a href="{$urlLogout}" class="logout">{"security.button.logout"|translate}</a>
{else}
    <span>{"security.label.user.anonymous"|translate}</span>
    |
    <a href="{$urlLogin}" class="login">{"security.button.login"|translate}</a>
{/if}
</div>