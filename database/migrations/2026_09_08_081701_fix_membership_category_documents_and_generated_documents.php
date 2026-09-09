
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Helper: Resolve document ID from its UNIQUE code
        |--------------------------------------------------------------------------
        |
        | Never rely on hard-coded document IDs because IDs can differ between
        | development, production, and SQLite test databases.
        |
        */

        $documentId = function (string $code): ?int {
            $id = DB::table('documents')
                ->where('code', $code)
                ->value('id');

            return $id !== null ? (int) $id : null;
        };

        /*
        |--------------------------------------------------------------------------
        | Helper: Check whether a membership category exists
        |--------------------------------------------------------------------------
        */

        $categoryExists = function (int $categoryId): bool {
            return DB::table('membership_categories')
                ->where('id', $categoryId)
                ->exists();
        };

        /*
        |--------------------------------------------------------------------------
        | Helper: Synchronize category → document mapping
        |--------------------------------------------------------------------------
        */

        $syncMapping = function (
            int $categoryId,
            string $documentCode,
            int $sortOrder
        ) use (
            $documentId,
            $categoryExists,
            $now
        ): void {

            /*
            |--------------------------------------------------------------------------
            | Do not create a foreign-key relationship if the category does not
            | exist in the current database.
            |--------------------------------------------------------------------------
            */

            if (! $categoryExists($categoryId)) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve the document using its stable UNIQUE code.
            |--------------------------------------------------------------------------
            */

            $resolvedDocumentId = $documentId($documentCode);

            /*
            |--------------------------------------------------------------------------
            | Do not create a foreign-key relationship if the document does
            | not exist in the current database.
            |--------------------------------------------------------------------------
            |
            | This is what makes the migration safe for SQLite test databases.
            |--------------------------------------------------------------------------
            */

            if ($resolvedDocumentId === null) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Update existing mapping or create it.
            |--------------------------------------------------------------------------
            */

            DB::table('membership_category_documents')->updateOrInsert(
                [
                    'membership_category_id' => $categoryId,
                    'document_id' => $resolvedDocumentId,
                ],
                [
                    'sort_order' => $sortOrder,
                    'status' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        };


        /*
        |--------------------------------------------------------------------------
        | EXPORTER - Category ID 1
        |--------------------------------------------------------------------------
        |
        | 1. Annual Membership Payment Receipt
        | 2. Charcoal Lifting Right - Exporter
        | 3. Membership Certificate - Regular Exporter
        | 4. Exporter Membership Confirmation Letter
        |
        */

        $syncMapping(
            1,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $syncMapping(
            1,
            'NACPDEAN-LIFTING-RIGHT-EXPORTER',
            2
        );

        $syncMapping(
            1,
            'NACPDEAN-MEMBERSHIP-REGULAR-EXPORTER',
            3
        );

        $syncMapping(
            1,
            'NACPDEAN-EXPORTER-CONFIRMATION',
            4
        );


        /*
        |--------------------------------------------------------------------------
        | RCG - Category ID 5
        |--------------------------------------------------------------------------
        |
        | 1. Annual Membership Payment Receipt
        | 2. Charcoal Lifting Right - RCG
        | 3. Membership Certificate - RCG Exporter
        | 4. Exporter Membership Confirmation Letter
        |
        */

        $syncMapping(
            5,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $syncMapping(
            5,
            'NACPDEAN-LIFTING-RIGHT-RCG',
            2
        );

        $syncMapping(
            5,
            'NACPDEAN-MEMBERSHIP-RCG-EXPORTER',
            3
        );

        $syncMapping(
            5,
            'NACPDEAN-EXPORTER-CONFIRMATION',
            4
        );


        /*
        |--------------------------------------------------------------------------
        | SUPPLIER - Category ID 2
        |--------------------------------------------------------------------------
        |
        | 1. Annual Membership Payment Receipt
        | 2. Charcoal Lifting Right - Supplier
        |
        */

        $syncMapping(
            2,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $syncMapping(
            2,
            'NACPDEAN-LIFTING-RIGHT-SUPPLIER',
            2
        );


        /*
        |--------------------------------------------------------------------------
        | DEALER - Category ID 3
        |--------------------------------------------------------------------------
        |
        | 1. Annual Membership Payment Receipt
        | 2. Charcoal Lifting Right - Dealer
        |
        */

        $syncMapping(
            3,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $syncMapping(
            3,
            'NACPDEAN-LIFTING-RIGHT-DEALER',
            2
        );


        /*
        |--------------------------------------------------------------------------
        | PRODUCER - Category ID 4
        |--------------------------------------------------------------------------
        |
        | 1. Annual Membership Payment Receipt
        | 2. Charcoal Lifting Right - Producer
        |
        */

        $syncMapping(
            4,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $syncMapping(
            4,
            'NACPDEAN-LIFTING-RIGHT-PRODUCER',
            2
        );


        /*
        |--------------------------------------------------------------------------
        | ALLOW "replaced" STATUS
        |--------------------------------------------------------------------------
        |
        | DocumentGenerationService uses "replaced" when an existing generated
        | document is superseded by a newly generated document.
        |
        | MySQL supports direct ENUM modification.
        | SQLite does not support MySQL's MODIFY ENUM syntax.
        |
        */

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE generated_documents
                MODIFY status ENUM(
                    'active',
                    'expired',
                    'revoked',
                    'replaced'
                ) NOT NULL DEFAULT 'active'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Helper: Resolve document ID from UNIQUE code
        |--------------------------------------------------------------------------
        */

        $documentId = function (string $code): ?int {
            $id = DB::table('documents')
                ->where('code', $code)
                ->value('id');

            return $id !== null ? (int) $id : null;
        };

        /*
        |--------------------------------------------------------------------------
        | Helper: Remove a mapping safely
        |--------------------------------------------------------------------------
        */

        $removeMapping = function (
            int $categoryId,
            string $documentCode
        ) use ($documentId): void {

            $resolvedDocumentId = $documentId($documentCode);

            if ($resolvedDocumentId === null) {
                return;
            }

            DB::table('membership_category_documents')
                ->where('membership_category_id', $categoryId)
                ->where('document_id', $resolvedDocumentId)
                ->delete();
        };


        /*
        |--------------------------------------------------------------------------
        | Remove mappings introduced by this migration
        |--------------------------------------------------------------------------
        */

        $removeMapping(
            2,
            'NACPDEAN-LIFTING-RIGHT-SUPPLIER'
        );

        $removeMapping(
            3,
            'NACPDEAN-LIFTING-RIGHT-DEALER'
        );

        $removeMapping(
            4,
            'NACPDEAN-LIFTING-RIGHT-PRODUCER'
        );


        /*
        |--------------------------------------------------------------------------
        | Helper: Restore sort order
        |--------------------------------------------------------------------------
        */

        $restoreSort = function (
            int $categoryId,
            string $documentCode,
            int $sortOrder
        ) use ($documentId): void {

            $resolvedDocumentId = $documentId($documentCode);

            if ($resolvedDocumentId === null) {
                return;
            }

            DB::table('membership_category_documents')
                ->where('membership_category_id', $categoryId)
                ->where('document_id', $resolvedDocumentId)
                ->update([
                    'sort_order' => $sortOrder,
                    'updated_at' => now(),
                ]);
        };


        /*
        |--------------------------------------------------------------------------
        | EXPORTER
        |--------------------------------------------------------------------------
        */

        $restoreSort(
            1,
            'NACPDEAN-MEMBERSHIP-REGULAR-EXPORTER',
            1
        );

        $restoreSort(
            1,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $restoreSort(
            1,
            'NACPDEAN-LIFTING-RIGHT-EXPORTER',
            2
        );

        $restoreSort(
            1,
            'NACPDEAN-EXPORTER-CONFIRMATION',
            4
        );


        /*
        |--------------------------------------------------------------------------
        | RCG
        |--------------------------------------------------------------------------
        */

        $restoreSort(
            5,
            'NACPDEAN-MEMBERSHIP-RCG-EXPORTER',
            1
        );

        $restoreSort(
            5,
            'NACPDEAN-ANNUAL-MEMBERSHIP-RECEIPT',
            1
        );

        $restoreSort(
            5,
            'NACPDEAN-LIFTING-RIGHT-RCG',
            2
        );

        $restoreSort(
            5,
            'NACPDEAN-EXPORTER-CONFIRMATION',
            4
        );


        /*
        |--------------------------------------------------------------------------
        | Restore original generated_documents ENUM on MySQL
        |--------------------------------------------------------------------------
        */

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE generated_documents
                MODIFY status ENUM(
                    'active',
                    'expired',
                    'revoked'
                ) NOT NULL DEFAULT 'active'
            ");
        }
    }
};
