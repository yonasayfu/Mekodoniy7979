<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Mekodonia Guest Mandate</title>
        <style>
            body {
                font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
                margin: 0;
                padding: 0;
                background: #f5f6fa;
                color: #0f172a;
            }

            .page {
                width: 210mm;
                min-height: 297mm;
                padding: 24mm;
                background: #fff;
                box-sizing: border-box;
            }

            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                border-bottom: 2px solid #e2e8f0;
                padding-bottom: 1rem;
            }

            header h1 {
                font-size: 1.75rem;
                margin: 0;
            }

            .section {
                margin-bottom: 1.8rem;
                padding-bottom: 1.5rem;
                border-bottom: 1px solid #e2e8f0;
            }

            .section h2 {
                font-size: 1.1rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                margin-bottom: 0.6rem;
                color: #475569;
            }

            .section table {
                width: 100%;
                border-collapse: collapse;
            }

            .section table td {
                padding: 0.35rem 0.5rem;
                vertical-align: top;
                font-size: 0.95rem;
            }

            .section table td.label {
                width: 30%;
                font-weight: 600;
                color: #0f172a;
            }

            .section table td.value {
                border-bottom: 1px dashed #94a3b8;
                min-height: 1.8rem;
            }

            .callout {
                background: #eef2ff;
                border-radius: 1rem;
                padding: 1rem;
                margin-top: 1rem;
                color: #312e81;
                border: 1px solid #c7d2fe;
                font-size: 0.9rem;
            }

            .signature-block {
                margin-top: 2rem;
                display: flex;
                justify-content: space-between;
                gap: 1rem;
            }

            .signature-block div {
                flex: 1;
                border-top: 1px solid #94a3b8;
                padding-top: 0.4rem;
                font-size: 0.85rem;
            }

            footer {
                margin-top: 3rem;
                font-size: 0.75rem;
                color: #475569;
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header>
                <div>
                    <p class="brand">Mekodonia Home Connect</p>
                    <h1>Guest Mandate & Consent</h1>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-size: 0.95rem;">
                        Date: {{ $date }}
                    </p>
                    <p style="margin: 0; font-weight: 600;">Mandate #{{ $reference }}</p>
                </div>
            </header>

            <section class="section">
                <h2>Donor information</h2>
                <table>
                    <tr>
                        <td class="label">Full name</td>
                        <td class="value">{{ $donor_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phone</td>
                        <td class="value">{{ $donor_phone }}</td>
                    </tr>
                    <tr>
                        <td class="label">Email</td>
                        <td class="value">{{ $donor_email }}</td>
                    </tr>
                    <tr>
                        <td class="label">Relationship</td>
                        <td class="value">{{ $relationship }}</td>
                    </tr>
                </table>
            </section>

            <section class="section">
                <h2>Pledge details</h2>
                <table>
                    <tr>
                        <td class="label">Monthly amount</td>
                        <td class="value">{{ $amount }} ETB</td>
                    </tr>
                    <tr>
                        <td class="label">Cadence</td>
                        <td class="value">{{ $cadence }}</td>
                    </tr>
                    <tr>
                        <td class="label">Deduction schedule</td>
                        <td class="value">{{ $schedule }}</td>
                    </tr>
                </table>

                <div class="callout">
                    I authorise Mekodonia Home Connect to share this mandate with the nominated branch and finance team. I understand they will notify me before processing each deduction and I will keep my transfer reference for reconciliation.
                </div>
            </section>

            <section class="section">
                <h2>Payment instructions</h2>
                <table>
                    <tr>
                        <td class="label">Telebirr</td>
                        <td class="value">
                            Scan the QR code shown on the guest donation page and paste the above reference in the note. Keep the confirmation slip and upload it when submitting proof.
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Bank transfer</td>
                        <td class="value">
                            Deposit to any listed account, include the reference above in the remarks, then sign below and re-upload the scanned mandate to this form.
                        </td>
                    </tr>
                </table>
            </section>

            <section class="section">
                <h2>Signature</h2>
                <div class="signature-block">
                    <div>
                        Donor signature<br />
                        {{ $signature_line }}
                    </div>
                    <div>
                        Branch witness<br />
                        {{ $branch_signature }}
                    </div>
                </div>
            </section>

            <footer>
                Mekodonia Home Connect • Caring for elders, one family at a time.
            </footer>
        </div>
    </body>
</html>
