<?php

namespace Jexactyl\Http\Controllers\Admin\Users;

use Illuminate\View\View;
use Jexactyl\Models\User;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Services\Users\UserUpdateService;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Http\Requests\Admin\Users\ResourceFormRequest;
use Throwable;

class ResourceController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(private AlertsMessageBag $alert, private UserUpdateService $updateService)
    {
    }

    /**
     * Display user resource page.
     */
    public function view(User $user): View
    {
        return view('admin.users.resources', ['user' => $user]);
    }

    /**
     * Update a user's resource balances.
     *
     * @throws DataValidationException
     * @throws RecordNotFoundException
     * @throws Throwable
     */
    public function update(ResourceFormRequest $request, User $user): RedirectResponse
    {
        $this->updateService
            ->setUserLevel(User::USER_LEVEL_ADMIN)
            ->handle($user, $request->normalize());

        $this->alert->success('User resources have been updated.')->flash();

        return redirect()->route('admin.users.resources', $user->id);
    }
}
