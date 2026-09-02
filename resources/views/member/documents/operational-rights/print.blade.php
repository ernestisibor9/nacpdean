<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <title>
        Print {{ $document->document_number }}
    </title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 12px;
            text-align: left;
        }

        th {
            width: 35%;
        }

        .actions {
            margin-bottom: 30px;
            text-align: center;
        }

        .actions button {
            padding: 10px 20px;
            cursor: pointer;
        }

        @media print {

            .actions {
                display: none;
            }

        }

    </style>

</head>


<body>


<div class="actions">

    <button onclick="window.print()">
        Print Document
    </button>

</div>


<div class="header">

    <h1>
        NACPDEAN
    </h1>

    <p>
        National Association of Charcoal Producers,
        Dealers, Exporters and Allied Nations
    </p>

</div>


<div class="title">

    {{ $document->document_title }}

</div>


<table>

    <tr>

        <th>
            Document Number
        </th>

        <td>
            {{ $document->document_number }}
        </td>

    </tr>


    <tr>

        <th>
            Reference Number
        </th>

        <td>
            {{ $document->reference_number }}
        </td>

    </tr>


    <tr>

        <th>
            Authentication Code
        </th>

        <td>
            {{ $document->authentication_code }}
        </td>

    </tr>


    <tr>

        <th>
            Member Name
        </th>

        <td>

            {{ $document->profile->surname ?? '' }}

            {{ $document->profile->first_name ?? '' }}

            {{ $document->profile->middle_name ?? '' }}

        </td>

    </tr>


    <tr>

        <th>
            Membership Number
        </th>

        <td>
            {{ $document->membership->membership_number ?? 'N/A' }}
        </td>

    </tr>


    <tr>

        <th>
            Membership Category
        </th>

        <td>
            {{ $document->category->name ?? 'N/A' }}
        </td>

    </tr>


    <tr>

        <th>
            Date Issued
        </th>

        <td>
            {{ $document->issued_at?->format('d F Y') }}
        </td>

    </tr>


    <tr>

        <th>
            Expiry Date
        </th>

        <td>
            {{ $document->expires_at?->format('d F Y') }}
        </td>

    </tr>


    <tr>

        <th>
            Status
        </th>

        <td>
            {{ strtoupper($document->status) }}
        </td>

    </tr>

</table>


</body>

</html>
