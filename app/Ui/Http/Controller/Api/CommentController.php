<?php

declare(strict_types=1);

namespace App\Ui\Http\Controller\Api;

use App\Exceptions\CommentAlreadyExistsException;
use App\Infrastructure\Model\Comment;
use App\Infrastructure\Model\Profile;
use App\Infrastructure\Model\Administrator;
use App\Ui\Http\Requests\Comment\CreateCommentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur pour la gestion des commentaires
 */
class CommentController extends Controller
{
    /**
     * Ajoute un commentaire sur un profil
     * @throws ValidationException
     * @throws CommentAlreadyExistsException
     */
    public function store(CreateCommentRequest $request, Profile $profile): JsonResponse
    {
        $user = $request->user();

        if (!$user instanceof Administrator) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Vérifier l'unicité du commentaire par admin/profil
        $this->ensureUniqueComment($user->id, $profile->id);

        $comment = Comment::create([
            'content' => $request->validated()['content'],
            'administrator_id' => $user->id,
            'profile_id' => $profile->id,
        ]);

        $comment->load(['administrator', 'profile']);

        return response()->json([
            'success' => true,
            'data' => $this->transformComment($comment)
        ], 201);
    }

    /**
     * Vérifie qu'un admin n'a pas déjà commenté le profil
     * @throws CommentAlreadyExistsException
     */
    private function ensureUniqueComment(int $administratorId, int $profileId): void
    {
        if (Comment::where('administrator_id', $administratorId)
            ->where('profile_id', $profileId)
            ->exists()) {

            throw new CommentAlreadyExistsException();
        }
    }

    /**
     * Réponse API
     *
     * @param Comment $comment
     * @return array<string, mixed>
     */
    private function transformComment(Comment $comment): array
    {
        $administrator = $comment->administrator;
        $profile = $comment->profile;

        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'administrator' => [
                'id' => $administrator->id,
                'email' => $administrator->email,
            ],
            'profile' => [
                'id' => $profile->id,
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
            ],
            'created_at' => $comment->created_at?->toISOString(),
            'updated_at' => $comment->updated_at?->toISOString(),
        ];
    }
}
