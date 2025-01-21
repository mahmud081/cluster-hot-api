<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\SharedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        $email = $request->email;
        $user = User::where(['email' => $email])->first();
        if (!is_null($user)) {
            return response()->json([
                'success' => true,
                'message' => $user
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(User $user)
    {
        if ($user->id != auth()->id()) {
            $sharer_id = auth()->id();
            $sharee_id = $user->id;

            $check_invitation = Invitation::where(['sharer_id' => $sharer_id, 'sharee_id' => $sharee_id])->first();
            if (is_null($check_invitation)) {
                $invitation = Invitation::create([
                    'sharer_id' => $sharer_id,
                    'sharee_id' => $sharee_id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $invitation,
                ], 201);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invitation $invitation)
    {
        $user_id = auth()->id();
        $invitation_sharee_id = $invitation->sharee_id;

        if ($user_id == $invitation_sharee_id) {
            try {
                $invitation->accepted = 1;
                $invitation->save();
                return response()->json([
                    'success' => true,
                    'message' => $invitation,
                ], 201);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation)
    {
        $user_id = auth()->id();
        $invitation_sharer_id = $invitation->sharer_id;
        $invitation_sharee_id = $invitation->sharee_id;

        if (($user_id == $invitation_sharer_id) || ($user_id == $invitation_sharee_id)) {
            try {
                SharedDevice::where(['sharer_id' => $invitation_sharer_id, 'sharee_id' => $invitation_sharee_id])->delete();
                $invitation->delete();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'System error. Please try again.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Request is invalid.']);
    }

    public function invitationsByMe()
    {
        $user_id = auth()->user()->id;
        $invitations = Invitation::where('sharer_id', $user_id)->get();

        $invitations_response = [];
        if (!is_null($invitations)) {
            foreach ($invitations as $invitation) {
                $sharee_id = $invitation->sharee_id;
                $sharee = User::find($sharee_id);
                $accepted = $invitation->accepted;

                if (!is_null($sharee)) {
                    $invitations_response[$invitation->id] = [
                        'sharee_id' => $sharee_id,
                        'name' => $sharee->name,
                        'email' => $sharee->email,
                        'accepted' => $accepted
                    ];
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => $invitations_response
        ], 201);
    }

    public function invitationsForMe()
    {
        $user_id = auth()->user()->id;
        $invitations = Invitation::where('sharee_id', $user_id)->get();

        $invitations_response = [];
        if (!is_null($invitations)) {
            foreach ($invitations as $invitation) {
                $sharer_id = $invitation->sharer_id;
                $sharer = User::find($sharer_id);
                $accepted = $invitation->accepted;

                if (!is_null($sharer)) {
                    $invitations_response[$invitation->id] = [
                        'sharer_id' => $sharer_id,
                        'name' => $sharer->name,
                        'email' => $sharer->email,
                        'accepted' => $accepted
                    ];
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => $invitations_response
        ], 201);
    }
}
