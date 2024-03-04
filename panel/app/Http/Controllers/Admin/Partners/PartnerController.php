<?php

namespace Jexactyl\Http\Controllers\Admin\Partners;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;
use Jexactyl\Contracts\Repository\UserRepositoryInterface;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Models\Partner;
use Jexactyl\Services\Users\UserCreationService;
use Jexactyl\Services\Users\UserDeletionService;
use Jexactyl\Services\Users\UserUpdateService;
use Prologue\Alerts\AlertsMessageBag;

class PartnerController extends Controller
{
    public function __construct(
        protected AlertsMessageBag $alert,
        protected UserCreationService $creationService,
        protected UserDeletionService $deletionService,
        protected Translator $translator,
        protected UserUpdateService $updateService,
        protected UserRepositoryInterface $repository,
        protected ViewFactory $view
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = Partner::with('user')
            ->select('partners.*', 'users.username')
            ->leftJoin('users', 'users.id', '=', 'partners.user_id')
            ->paginate(50);


        return $this->view->make('admin.partners.index', ['partners' => $partners]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->view->make('admin.partners.new', [

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1|exists:users,id|unique:partners,user_id',
            'partner_discount' => 'required|numeric|min:0|max:100',
            'referral_discount' => 'required|numeric|min:0|max:100',
            'referral_reward' => 'required|numeric|min:0|max:100',
        ]);
        $partner = Partner::create([
            'user_id' => $request->input('user_id'),
            'partner_discount' => $request->input('partner_discount'),
            'referral_discount' => $request->input('referral_discount'),
            'referral_reward' => $request->input('referral_reward'),
        ]);
        $this->alert->success($this->translator->get('admin/partner.notices.partner_created'))->flash();

        return redirect()->route('admin.partners.view', $partner->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $partner = Partner::with('user')
            ->select('partners.*', 'users.username', 'users.email')
            ->leftJoin('users', 'users.id', '=', 'partners.user_id')
            ->first();
        return $this->view->make('admin.partners.view', [
            'partner' => $partner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'partner_discount' => 'required|numeric|min:0|max:100',
            'referral_discount' => 'required|numeric|min:0|max:100',
            'referral_reward' => 'required|numeric|min:0|max:100',
        ]);

        $partner = Partner::findOrFail($id);

        $partner->partner_discount = $request->input('partner_discount');
        $partner->referral_discount = $request->input('referral_discount');
        $partner->referral_reward = $request->input('referral_reward');
        $partner->save();

        $this->alert->success(trans('admin/partner.notices.partner_updated'))->flash();

        return redirect()->route('admin.partners.view', $partner->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        Partner::findOrFail($id)->delete();

        $this->alert->success(trans('admin/partner.notices.partner_deleted'))->flash();


        return redirect()->route('admin.partners');
    }
}
