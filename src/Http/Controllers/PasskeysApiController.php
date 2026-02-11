<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;
use Throwable;
use TypiCMS\Modules\Core\Models\User;
use Webauthn\PublicKeyCredentialCreationOptions;

final class PasskeysApiController extends BaseApiController
{
    public function getPasskeys(Request $request, User $user): JsonResponse
    {
        $passkeys = $user->passkeys()->get(['id', 'name', 'last_used_at']);

        return response()->json($passkeys);
    }

    public function store(Request $request): void
    {
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                $this->currentUser(),
                $request->passkey,
                $request->options ?? $this->previouslyGeneratedPasskeyOptions(),
                request()->getHost(),
                ['name' => (string) $request->string('name')],
            );
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ])->errorBag('passkeyForm');
        }
    }

    public function destroy(int $passkey): void
    {
        Passkey::query()
            ->where('id', $passkey)
            ->delete();
    }

    public function currentUser(): Authenticatable&HasPasskeys
    {
        return auth()->user();
    }

    protected function generatePasskeyOptions(): string|PublicKeyCredentialCreationOptions
    {
        $generatePassKeyOptionsAction = Config::getAction(
            'generate_passkey_register_options',
            GeneratePasskeyRegisterOptionsAction::class,
        );

        $options = $generatePassKeyOptionsAction->execute($this->currentUser());

        session()->put('passkey-registration-options', $options);

        return $options;
    }

    protected function previouslyGeneratedPasskeyOptions(): ?string
    {
        return session()->pull('passkey-registration-options');
    }
}
