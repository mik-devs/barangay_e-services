<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Business Permit</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h4, .header h3, .header p {
            margin: 2px 0;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 25px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .salutation {
            margin-bottom: 15px;
            font-weight: bold;
        }
        .paragraph {
            text-align: justify;
            text-indent: 40px;
            margin-bottom: 10px;
        }
        .business-box {
            border: 1px dashed #666;
            padding: 15px 20px;
            margin: 20px 0;
            background-color: #f9f9f9;
        }
        .business-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .business-box td {
            padding: 4px 0;
            vertical-align: top;
        }
        .clearance-details {
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .footer-table td {
            vertical-align: bottom;
            padding: 0;
        }
        .thumbmark-box {
            width: 120px;
            height: 140px;
            border: 1px solid #333;
            text-align: center;
            padding-top: 40px;
            font-size: 11px;
            color: #666;
        }
        .signature-container {
            text-align: center;
            width: 250px;
            margin-left: auto;
        }
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: bold;
        }
        .validity {
            margin-top: 15px;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <!-- Header / Letterhead -->
    <div class="header">
        <p>Republic of the Philippines</p>
        <p>Province of Davao del Sur</p>
        <p>Municipality of Kiblawan</p>
        <h3>BARANGAY PASIG</h3>
        <p><strong>OFFICE OF THE PUNONG BARANGAY</strong></p>
    </div>

    <!-- Title -->
    <div class="title">
        Barangay Business Permit
    </div>

    <!-- Body Content -->
    <div class="salutation">TO WHOM IT MAY CONCERN:</div>
    
    <div class="paragraph">
        Pursuant to the provisions of the Local Government Code and relevant Barangay Ordinances, 
        permission is hereby granted to operate a business establishment within the jurisdiction of this barangay, 
        having complied with the requirements prescribed by law:
    </div>

    <!-- Business Details Box -->
    <div class="business-box">
        <table>
            <tr>
                <td style="width: 35%;"><strong>Business Name:</strong></td>
                <td style="width: 65%;">{{ $documentRequest->remarks ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Owner / Proprietor:</strong></td>
                <td>
                    @php
                        $user = $documentRequest->user;
                        $profile = $user->profile ?? ($user->residentProfile ?? null);
                        $firstName = $user->first_name ?? $profile->first_name ?? '';
                        $middleName = $user->middle_name ?? $profile->middle_name ?? '';
                        $lastName = $user->last_name ?? $profile->last_name ?? '';
                        $suffix = $user->suffix ?? $profile->suffix ?? '';
                        
                        $cleanVal = fn($v) => (strcasecmp(trim($v), 'n/a') === 0 || trim($v) === '') ? null : trim($v);
                        $ownerName = implode(' ', array_filter([$cleanVal($firstName), $cleanVal($middleName), $cleanVal($lastName), $cleanVal($suffix)])) ?: ($user->name ?? 'N/A');
                    @endphp
                    {{ $ownerName }}
                </td>
            </tr>
            <tr>
                <td><strong>Nature of Business:</strong></td>
                <td>{{ $documentRequest->document_type }}</td>
            </tr>
            <tr>
                <td><strong>Business Address:</strong></td>
                <td>Barangay Pasig, Kiblawan, Davao del Sur</td>
            </tr>
        </table>
    </div>

    <div class="paragraph">
        This permit is issued for the purpose of <strong>{{ $documentRequest->purpose }}</strong> 
        and is subject to strict compliance with existing sanitation, environmental, and local peace and order ordinances. 
        Any violation of the rules and regulations of this barangay shall be grounds for the revocation of this permit.
    </div>

    <div class="validity">
        <strong>Valid Until:</strong> December 31, {{ now()->format('Y') }} unless sooner revoked.
    </div>

    <div class="paragraph" style="margin-top: 15px;">
        Given this {{ now()->format('jS') }} day of {{ now()->format('F, Y') }} at Barangay Pasig, 
        Kiblawan, Davao del Sur, Philippines.
    </div>

    <!-- Tracking Info -->
    <div class="clearance-details">
        <p><strong>Tracking No:</strong> {{ $documentRequest->tracking_number }}</p>
    </div>

    <!-- Footer: Thumbmark Box & Signatory Table -->
    <table class="footer-table">
        <tr>
            <td>
                <div class="thumbmark-box">
                    Owner's Signature / Thumbmark
                </div>
            </td>
            <td>
                <div class="signature-container">
                    @php
                        $sigPath = ($signatory && $signatory->signature) ? public_path('storage/' . $signatory->signature) : null;
                        $punongBarangayName = $signatory->full_name ?? $signatory->name ?? 'HON. PUNONG BARANGAY';
                    @endphp

                    @if($sigPath && file_exists($sigPath))
                        <div style="margin-bottom: -25px;">
                            <img src="{{ $sigPath }}" alt="Digital Signature" style="max-height: 55px; max-width: 180px; object-fit: contain;">
                        </div>
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    
                    <div style="height: 15px;"></div>
                    
                    <div class="signature-line" style="text-transform: uppercase;">
                        {{ $punongBarangayName }}
                    </div>
                    <div style="text-align: center; font-size: 11px; margin-top: 3px; font-weight: bold;">PUNONG BARANGAY</div>
                    <div style="text-align: center; font-size: 10px; color: #555;">Authorized Signature</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>