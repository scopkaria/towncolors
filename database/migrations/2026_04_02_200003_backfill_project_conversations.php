<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill: create a conversation for each project that already has messages,
 * link its participants (client + freelancer + all admins), and
 * stamp conversation_id on every existing message row.
 */
return new class extends Migration
{
    public function up(): void
    {
        $adminIds = DB::table('users')
            ->where('role', 'admin')
            ->pluck('id')
            ->all();

        $projectIds = DB::table('messages')
            ->whereNotNull('project_id')
            ->whereNull('conversation_id')
            ->distinct()
            ->pluck('project_id');

        foreach ($projectIds as $projectId) {
            $project = DB::table('projects')->where('id', $projectId)->first();
            if (! $project) {
                continue;
            }

            // Find or create the conversation for this project
            $conversation = DB::table('conversations')
                ->where('type', 'project')
                ->where('project_id', $projectId)
                ->first();

            if ($conversation) {
                $conversationId = $conversation->id;
            } else {
                $conversationId = DB::table('conversations')->insertGetId([
                    'type'       => 'project',
                    'project_id' => $projectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Participants: admins + project client + project freelancer
            $participantIds = array_merge($adminIds, array_filter([
                $project->client_id     ?? null,
                $project->freelancer_id ?? null,
            ]));

            foreach (array_unique($participantIds) as $userId) {
                DB::table('conversation_participants')->insertOrIgnore([
                    'conversation_id' => $conversationId,
                    'user_id'         => $userId,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // Stamp every message for this project
            DB::table('messages')
                ->where('project_id', $projectId)
                ->whereNull('conversation_id')
                ->update(['conversation_id' => $conversationId]);
        }
    }

    public function down(): void
    {
        DB::table('messages')->update(['conversation_id' => null]);
        DB::table('conversation_participants')->delete();
        DB::table('conversations')->where('type', 'project')->delete();
    }
};
