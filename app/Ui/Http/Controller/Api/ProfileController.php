<?php

declare(strict_types=1);

namespace App\Ui\Http\Controller\Api;

use App\Infrastructure\Model\Profile;
use App\Infrastructure\Model\Administrator;
use App\Ui\Http\Requests\Profile\CreateProfileRequest;
use App\Ui\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;

/**
 * Contrôleur pour la gestion des profils
 */
class ProfileController extends Controller
{
    /**
     * Crée un nouveau profil
     */
    public function store(CreateProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        if (!$user instanceof Administrator) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            if ($imageFile instanceof UploadedFile) {
                $imagePath = $imageFile->store('profiles', 'public');
            }
        }

        $profile = Profile::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'administrator_id' => $user->id,
            'status' => $validated['status'] ?? 'pending',
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'profile' => $this->transformProfile($profile->load('administrator'), includeAdmin: true)
        ], 201);
    }

    /**
     *  PUBLIC Récupère tous les profils actifs sans status
     */
    public function publicIndex(): JsonResponse
    {
        $profiles = Profile::active()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $profiles->map(fn(Profile $profile): array => $this->transformProfile($profile)),
            'count' => $profiles->count()
        ]);
    }

    /**
     * ADMIN Récupère tous les profils
     */
    public function adminIndex(): JsonResponse
    {
        $profiles = Profile::withAdministrator()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $profiles->map(fn(Profile $profile): array => $this->transformProfile($profile, includeAdmin: true)),
            'count' => $profiles->count()
        ]);
    }

    /**
     * Met à jour un profil
     */
    public function update(UpdateProfileRequest $request, Profile $profile): JsonResponse
    {
        $profile->update($request->validated());

        return response()->json([
            'success' => true,
            'profile' => $this->transformProfile($profile->loadMissing('administrator'), includeAdmin: true)
        ]);
    }

    /**
     * Met à jour l'image d'un profil
     */
    public function updateProfilImage(Request $request, Profile $profile): JsonResponse
    {
        $request->validate(['image' => 'required|image|max:2048']);

        $profile->deleteImage();

        $imageFile = $request->file('image');
        $imagePath = null;

        if ($imageFile instanceof UploadedFile) {
            $imagePath = $imageFile->store('profiles', 'public');
        }

        $profile->update(['image_path' => $imagePath]);

        $refreshedProfile = $profile->fresh();

        if (!$refreshedProfile instanceof Profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found after update'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'profil_image' => [
                'id' => $refreshedProfile->id,
                'image_url' => $refreshedProfile->image_url,
                'updated_at' => $refreshedProfile->updated_at?->toISOString(),
            ]
        ]);
    }

    /**
     * Supprime un profil
     */
    public function destroy(Profile $profile): JsonResponse
    {
        $profile->deleteImage();
        $profile->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Transforme un profil pour la réponse API
     *
     * @param Profile $profile
     * @param bool $includeAdmin
     * @return array<string, mixed>
     */
    private function transformProfile(Profile $profile, bool $includeAdmin = false): array
    {
        $data = [
            'id' => $profile->id,
            'first_name' => $profile->first_name,
            'last_name' => $profile->last_name,
            'image_url' => $profile->image_url,
            'created_at' => $profile->created_at?->toISOString(),
            'updated_at' => $profile->updated_at?->toISOString(),
        ];

        if ($includeAdmin) {
            $data['status'] = $profile->status;
            $data['administrator'] = $this->transformAdministrator($profile->administrator);
        }

        return $data;
    }

    /**
     * Réponse API
     *
     * @param Administrator|null $administrator
     * @return array<string, mixed>|null
     */
    private function transformAdministrator(?Administrator $administrator): ?array
    {
        if (!$administrator instanceof Administrator) {
            return null;
        }

        return [
            'id' => $administrator->id,
            'email' => $administrator->email,
        ];
    }
}
