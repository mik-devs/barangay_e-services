<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Residency</title>
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
            margin: 30px 0;
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
            margin-bottom: 15px;
        }
        .clearance-details {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
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
        Certificate of Residency
    </div>

    <!-- Body Content -->
    <div class="salutation">TO WHOM IT MAY CONCERN:</div>
    
    <div class="paragraph">
        @php
            $user = $documentRequest->user;
            $profile = $user->profile ?? ($user->residentProfile ?? null);

            $firstName = $user->first_name ?? $profile->first_name ?? '';
            $middleName = $user->middle_name ?? $profile->middle_name ?? '';
            $lastName = $user->last_name ?? $profile->last_name ?? '';
            $suffix = $user->suffix ?? $profile->suffix ?? '';
            
            $cleanVal = function($val) {
                $val = trim($val);
                return (strcasecmp($val, 'n/a') === 0 || $val === '') ? null : $val;
            };

            $nameParts = array_filter([
                $cleanVal($firstName),
                $cleanVal($middleName),
                $cleanVal($lastName),
                $cleanVal($suffix),
            ]);

            $residentName = implode(' ', $nameParts);
            if (empty($residentName)) {
                $residentName = $user->name ?? 'N/A';
            }

            $birthDate = $profile->birth_date ?? $user->birth_date ?? null;
            $age = $birthDate ? \Carbon\Carbon::parse($birthDate)->age : null;
            $civilStatus = $profile->civil_status ?? 'of legal age';
        @endphp

        This is to certify that <strong>{{ $residentName }}</strong>, 
        @if($age)
            <strong>{{ $age }}</strong> years old, 
        @else
            of legal age, 
        @endif
        <strong>{{ $civilStatus ?? '' }}</strong>, is a bonafide and permanent resident of Barangay Pasig, 
        Kiblawan, Davao del Sur, and whose signature and thumbmark appear hereon.
    </div>

    <div class="paragraph">
        Based on the records kept in this office, he/she has been residing in the said barangay 
        and is known to be a person of good moral character and a law-abiding citizen.
    </div>

    <div class="paragraph">
        This certification is being issued upon the request of the interested party for 
        <strong>{{ $documentRequest->purpose }}</strong> and for whatever legal intent and purpose it may serve him/her best.
    </div>

    <div class="paragraph">
        Issued this {{ now()->format('jS') }} day of {{ now()->format('F Y') }} at Barangay Pasig, 
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
                    Right Thumbmark
                </div>
            </td>
            <td>
                <div class="signature-container">
                    @php
                        $sigPath = ($signatory && $signatory->signature) ? public_path('storage/' . $signatory->signature) : null;
                        
                        $punongBarangayName = '';
                        if ($signatory) {
                            $punongBarangayName = $signatory->full_name ?? $signatory->name;
                        }
                        if (empty(trim($punongBarangayName))) {
                            $punongBarangayName = 'HON. PUNONG BARANGAY'; 
                        }
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