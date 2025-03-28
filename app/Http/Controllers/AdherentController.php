<?php

namespace App\Http\Controllers;

use App\Models\Adhesion;
use App\Models\User;
use App\Models\Group;
use App\Models\Status;
use App\Models\Site;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdherentController extends Controller
{

    /**
     * Get all users with their adhesions
     *
     */
    public function index()
    {
        $users = User::getAllUsersWithRelations();
        $groups = Group::all();
        $sites = Site::all();
        $status = Status::all();
        return view('Adherents', [
            'users' => $users,
            'groups' => $groups,
            'sites' => $sites,
            'status' => $status,
            'admin_page'=>true
        ]);
    }

    /**
     * Add a new user with an adhesion if requested
     */
    public function addAdherent(Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:status,status_id',
            'group_id' => 'required|exists:group,group_id',
            'site_id' => 'required|exists:site,site_id',
            'last_name' => 'required|string|max:191',
            'first_name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'email_imt' => 'sometimes|email|max:191|unique:users,email_imt',
            'phone_number' => 'sometimes|string|max:191',
            'photo_release' => 'sometimes|boolean',
            'photo_consent' => 'sometimes|boolean',
            'adhesion_valid' => 'sometimes|boolean',
        ]);

        $password = $this->generatePassword();
        $validated['password'] = bcrypt($password);
        $validated['is_admin'] = false;

        $user = User::create($validated);

        if (isset($validated['adhesion_valid']) && $validated['adhesion_valid']) {
            $adhesion_valid = $validated['adhesion_valid'];
        } else {
            $adhesion_valid = false;
        }

        if ($user) {
            $adhesion = Adhesion::createForUser($user->user_id);
            $site_user = SiteUser::create([
                'site_id' => $validated['site_id'],
                'user_id' => $user->user_id
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Adhérent ajouté avec succès',
                'data' => [
                    'user' => $user,
                    'adhesion' => $adhesion,
                    'password' => $password,
                    'site_user' => $site_user
                ]
            ], 201);
            // quand la vue sera implémentée
            // return redirect()->route('adherents')->with(['success' => 'Adhérent ajoutée avec succès', 'user' => $user, 'adhesion' => $adhesion, 'password' => $password]);
        }

        return response()->json([
            'status' => 'erreur',
            'message' => 'Aucun adhérent n\'a été ajoutée',
        ]);
    }

    /**
     * Generate a secure random password
     *
     * @param int $length The length of the password to generate
     * @return string The generated password
     */
    private function generatePassword($length = 10)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+';

        $all = $lowercase . $uppercase . $numbers . $special;

        $password = '';

        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Get all users with adhesions for a specific year
     *
     * @param Request $request
     * @param int|null $year The year to filter by (defaults to current year)
     * @return \Illuminate\Http\Response
     */
    public function getAdherentsByYear(Request $request, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        // Convert year to integer
        $year = (int)$year;
        $startDate = $year . '-09-01';
        $endDate = ($year + 1) . '-08-31';

        $adherents = User::getUsersWithAdhesionsByDateRange($startDate, $endDate);

        return response()->json([
            'year' => $year,
            'adherents' => $adherents
        ]);
    }

    /**
     * Update a user's adhesion
     *
     * @param Request $request
     * @param int $id Adhesion ID
     */
    public function updateAdherent(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'status' => 'erreur',
                'message' => 'Adhérent non trouvé',
            ], 404);
        }

        $validated = $request->validate([
            'status_id' => 'sometimes|exists:status,status_id',
            'group_id' => 'sometimes|exists:group,group_id',
            'site_id' => 'sometimes|exists:site,site_id',
            'last_name' => 'sometimes|string|max:191',
            'first_name' => 'sometimes|string|max:191',
            'email' => 'sometimes|email|max:191|unique:users,email,'.$user_id.',user_id',
            'email_imt' => 'sometimes|email|max:191|unique:users,email_imt,'.$user_id.',user_id',
            'phone_number' => 'sometimes|string|max:191',
            'photo_release' => 'sometimes|boolean',
            'photo_consent' => 'sometimes|boolean',
            'adhesion_valid' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        if (isset($validated['site_id'])) {
            SiteUser::where('user_id', $user_id)->update([
                'site_id' => $validated['site_id']
            ]);
        }

        if (isset($validated['adhesion_valid']) && $validated['adhesion_valid'] && !$user->hasUpToDateAdhesion()) {
            $adhesion = Adhesion::createForUser($user->user_id);
            $adhesionData = $adhesion;
        } else {
            $adhesionData = $user->adhesions()->orderBy('date_adhesion', 'desc')->first();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Adhérent mis à jour avec succès',
            'data' => [
                'user' => $user,
                'adhesion' => $adhesionData
            ]
        ], 200);
    }

    /**
     * Delete a user's adhesion
     *
     * @param int $id Adhesion ID
     */
    public function deleteAdherent($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'status' => 'erreur',
                'message' => 'Adhérent non trouvé',
            ], 404);
        }

        if ($user->is_admin) {
            return response()->json([
                'status' => 'erreur',
                'message' => 'Vous ne pouvez pas summprimer un administrateur',
            ], 403);
        }

        $userData = $user->toArray();

        try {
            // Begin transaction to ensure all deletions succeed or none do
            DB::beginTransaction();

            // Delete associated subventions first
            DB::table('subvention')->where('user_id', $user_id)->delete();

            // Delete other associated records
            SiteUser::where('user_id', $user_id)->delete();
            Adhesion::where('user_id', $user_id)->delete();

            // Delete any family members
            DB::table('family_members')->where('user_id', $user_id)->delete();

            // Delete the user
            $user->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Adhérent supprimé avec succès',
                'data' => [
                    'user' => $userData
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'erreur',
                'message' => 'Erreur lors de la suppression de l\'adhérent: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single adherent's data
     *
     * @param int $user_id The ID of the adherent to fetch
     * @return \Illuminate\Http\Response
     */
    public function getAdherent($user_id)
    {
        $user = User::with(['group', 'sites', 'adhesions'])->find($user_id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Adhérent non trouvé'
            ], 404);
        }

        // Get the user's site
        $site = $user->sites->first();
        $site_id = $site ? $site->site_id : null;

        // Get the latest adhesion
        $latestAdhesion = $user->adhesions->sortByDesc('date_adhesion')->first();
        $adhesion_valid = $latestAdhesion ? true : false;

        $userData = $user->toArray();
        $userData['site_id'] = $site_id;
        $userData['adhesion_valid'] = $adhesion_valid;

        return response()->json($userData);
    }
}
