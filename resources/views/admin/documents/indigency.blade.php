<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Indigency</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; line-height: 1.6; margin: 0; padding: 30px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header p, .header h3 { margin: 2px 0; }
        .title { text-align: center; font-size: 20px; font-weight: bold; margin: 30px 0; text-transform: uppercase; letter-spacing: 1px; }
        .salutation { margin-bottom: 15px; font-weight: bold; }
        .paragraph { text-align: justify; text-indent: 40px; margin-bottom: 15px; }
        .clearance-details { margin-top: 20px; margin-bottom: 10px; font-size: 13px; }
        .footer-table { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .footer-table td { vertical-align: bottom; padding: 0; }
        .thumbmark-box { width: 120px; height: 140px; border: 1px solid #333; text-align: center; padding-top: 40px; font-size: 11px; color: #666; }
        .signature-container { text-align: center; width: 250px; margin-left: auto; }
        .signature-line { border-top: 1px solid #000; padding-top: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <p>Republic of the Philippines</p>
        <p>Province of Davao del Sur</p>
        <p>Municipality of Kiblawan</p>
        <h3>BARANGAY PASIG</h3>
        <p><strong>OFFICE OF THE PUNONG BARANGAY</strong></p>
    </div>

    <div class="title">Certificate of Indigency</div>

    <div class="salutation">TO WHOM IT MAY CONCERN:</div>
    
    <div class="paragraph">
        @php
            $user = $documentRequest->user;
            $profile = $user->profile ?? ($user->residentProfile ?? null);
            $firstName = $user->first_name ?? $profile->first_name ?? '';
            $middleName = $user->middle_name ?? $profile->middle_name ?? '';
            $lastName = $user->last_name ?? $profile->last_name ?? '';
            $suffix = $user->suffix ?? $profile->suffix ?? '';
            
            $cleanVal = fn($v) => (strcasecmp(trim($v), 'n/a') === 0 || trim($v) === '') ? null : trim($v);
            $residentName = implode(' ', array_filter([$cleanVal($firstName), $cleanVal($middleName), $cleanVal($lastName), $cleanVal($suffix)])) ?: ($user->name ?? 'N/A');
            $age = ($profile->birth_date ?? $user->birth_date ?? null) ? \Carbon\Carbon::parse($profile->birth_date ?? $user->birth_date)->age : null;
            $civilStatus = $profile->civil_status ?? 'single/married';
        @endphp

        This is to certify that <strong>{{ $residentName }}</strong>, 
        @if($age) <strong>{{ $age }}</strong> years old, @else of legal age, @endif
        <strong>{{ $civilStatus }}</strong>, is a bonafide and permanent resident of Barangay Pasig, Kiblawan, Davao del Sur.
    </div>

    <div class="paragraph">
        This is to certify further that based on the records, assessment, and prevailing circumstances known to this office, the abovenamed person belongs to an <strong>indigent family</strong> residing within our jurisdiction, having limited financial resources and a low annual income.
    </div>

    <div class="paragraph">
        This certification is being issued upon the request of the interested party to support their application for <strong>{{ $documentRequest->purpose }}</strong> and for whatever legal intent and purpose it may serve best.
    </div>

    <div class="paragraph">
        Issued this {{ now()->format('jS') }} day of {{ now()->format('F Y') }} at Barangay Pasig, Kiblawan, Davao del Sur, Philippines.
    </div>

    <div class="clearance-details">
        <p><strong>Tracking No:</strong> {{ $documentRequest->tracking_number }}</p>
    </div>

    <table class="footer-table">
        <tr>
            <td>
                <div class="thumbmark-box">Right Thumbmark</div>
            </td>
            <td>
                <div class="signature-container">
                    @php
                        $sigPath = ($signatory && $signatory->signature) ? public_path('storage/' . $signatory->signature) : null;
                        $punongBarangayName = $signatory->full_name ?? $signatory->name ?? 'HON. PUNONG BARANGAY';
                    @endphp
                    @if($sigPath && file_exists($sigPath))
                        <div style="margin-bottom: -25px;"><img src="{{ $sigPath }}" alt="Signature" style="max-height: 55px; max-width: 180px; object-fit: contain;"></div>
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="height: 15px;"></div>
                    <div class="signature-line" style="text-transform: uppercase;">{{ $punongBarangayName }}</div>
                    <div style="text-align: center; font-size: 11px; margin-top: 3px; font-weight: bold;">PUNONG BARANGAY</div>
                    <div style="text-align: center; font-size: 10px; color: #555;">Authorized Signature</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>