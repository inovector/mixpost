<x-mail::message>
    <x-mail::panel>
        {{ str_replace([':name', ':provider'], [$account->name, $account->providerName()], 'It\'s looks like we\'ve lost connection to your :name (:provider) account.') }}

        While the connection is lost, Mixpost cannot publish posts or provide analytics for this account. Please,
        reconnect your account as soon as possible!
    </x-mail::panel>

    To reconnect your account, click the button below and then click the "Add account" button.

    <x-mail::button :url="$url">
        Accounts
    </x-mail::button>
</x-mail::message>
