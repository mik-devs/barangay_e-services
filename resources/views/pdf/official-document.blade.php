<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $doc->tracking_number }} - Official Document</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h3, .header h4, .header p {
            margin: 2px 0;
        }
        .title {
            text-align: center;
            text-transform: uppercase;
            font-size: 20px;
            font-weight: bold;
            margin: 40px 0;
            letter-spacing: 1px;
        }
        .content {
            font-size: 14px;
            text-align: justify;
            margin-bottom: 40px;
        }
        .details-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 13px;
        }
        .footer-signatures {
            margin-top: 60px;
            width: 100%;
        }
        .signature-container {
            width: 250px;
            margin-left: auto;
            text-align: center;
        }
        .signature-line {
            width: 100%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 5px;
            font-weight: bold;
        }
        .tracking-info {
            font-size: 11px;
            color: #666;
            margin-top: 50px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Header / Barangay Letterhead -->
    <div class="header">
        <h4>Republic of the Philippines</h4>
        <h4>Province of Davao del Sur</h4>
        <h4>Municipality of Kiblawan</h4>
        <h3>BARANGAY E-PORTAL SYSTEM</h3>
        <p>Office of the Punong Barangay</p>
    </div>

    <!-- Document Title -->
    <div class="title">
        {{ $doc->document_type }}
    </div>

    <!-- Main Content Body -->
    <div class="content">
        <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
        
        <p>
            This is to certify that 
            <strong>
                @if(isset($resident) && $resident)
                    {{ trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? '')) ?: ($resident->name ?? 'N/A') }}
                @else
                    N/A
                @endif
            </strong>, 
            of legal age, is a permanent resident of this barangay and is known to be of good moral character and a law-abiding citizen.
        </p>

        <p>
            This certification is being issued upon the request of the above-named person for the following purpose: 
            <br><strong>"{{ $doc->purpose }}"</strong>
        </p>

        <p>
            Issued this <strong>{{ \Carbon\Carbon::now()->format('jS') }}</strong> day of <strong>{{ \Carbon\Carbon::now()->format('F, Y') }}</strong> at Barangay Hall, Kiblawan, Davao del Sur.
        </p>
    </div>

    <!-- Additional Info Box -->
    <div class="details-box">
        <strong>Document Details:</strong><br>
        Tracking Number: {{ $doc->tracking_number }}<br>
        Fee Paid: ₱{{ number_format($doc->fee ?? 0, 2) }}<br>
        Status: {{ ucfirst($doc->status) }}
    </div>

    <!-- Signatures -->
    <table class="footer-signatures">
        <tr>
            <td></td>
            <td>
                <div class="signature-container">
                    @php
                    
                        $sigPath = ($signatory && $signatory->signature) ? public_path('storage/' . $signatory->signature) : null;
                        
                        
                        $punongBarangayName = '';
                        if ($signatory) {
                            $punongBarangayName = trim(($signatory->first_name ?? '') . ' ' . ($signatory->middle_name ?? '') . ' ' . ($signatory->last_name ?? ''));
                        }
                        if (empty($punongBarangayName)) {
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

    <!-- Tracking / Verification Footnote -->   
    <div class="tracking-info">
        <span>Tracking Code: {{ $doc->tracking_number }}</span><br>
        <span>Verify this document online through the Barangay E-Portal Verification System.</span>
    </div>

</body>
</html>