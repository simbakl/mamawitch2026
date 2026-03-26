<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mama Witch - Fiche Technique</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }

        .header { background: #1a1a1a; color: white; padding: 30px; text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 28px; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 5px; }
        .header p { font-size: 12px; color: #999; }

        .section { margin: 0 25px 20px; }
        .section-title { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #1a1a1a; padding-bottom: 5px; margin-bottom: 10px; }

        .member-block { margin-bottom: 15px; page-break-inside: avoid; }
        .member-name { font-size: 13px; font-weight: bold; background: #f0f0f0; padding: 5px 10px; margin-bottom: 5px; }
        .member-instruments { font-size: 11px; color: #666; font-style: italic; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table th { background: #e0e0e0; text-align: left; padding: 4px 8px; font-size: 10px; text-transform: uppercase; }
        table td { padding: 4px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        table .category { font-weight: bold; font-size: 10px; color: #666; text-transform: uppercase; }

        .requirements { margin-top: 5px; }
        .req-item { margin-bottom: 3px; }
        .req-label { font-weight: bold; color: #444; }

        .global-info { display: table; width: 100%; }
        .global-col { display: table-cell; width: 33%; padding: 5px; vertical-align: top; }
        .global-col strong { display: block; font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 2px; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; padding: 10px; border-top: 1px solid #ddd; }
        .contact { font-size: 10px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h1>MAMA WITCH</h1>
        <p>FICHE TECHNIQUE</p>
        <p class="contact" style="margin-top: 10px;">contact@mamawitch.fr &bull; mamawitch.fr</p>
    </div>

    {{-- LINE-UP --}}
    <div class="section">
        <div class="section-title">Line-up</div>
        <table>
            <tr>
                <th>Nom</th>
                <th>Instrument(s)</th>
            </tr>
            @foreach ($members as $member)
                <tr>
                    <td><strong>{{ $member->name }}</strong></td>
                    <td>{{ $member->instruments }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    {{-- TIMING --}}
    @if ($global['setup_time'] || $global['soundcheck_time'] || $global['teardown_time'])
        <div class="section">
            <div class="section-title">Timing</div>
            <div class="global-info">
                @if ($global['setup_time'])
                    <div class="global-col"><strong>Montage</strong> {{ $global['setup_time'] }}</div>
                @endif
                @if ($global['soundcheck_time'])
                    <div class="global-col"><strong>Balance</strong> {{ $global['soundcheck_time'] }}</div>
                @endif
                @if ($global['teardown_time'])
                    <div class="global-col"><strong>Demontage</strong> {{ $global['teardown_time'] }}</div>
                @endif
            </div>
        </div>
    @endif

    {{-- STAGE PLAN --}}
    @if (count($stagePlanElements) > 0)
        @php
            $stageW = $stagePlan->stage_width ?: 800;
            $stageD = $stagePlan->stage_depth ?: 500;
            $canvasWidthPx = 500;
            $canvasHeightPx = round($canvasWidthPx * $stageD / $stageW);

            $colors = [
                'guitar_amp' => '#ef4444', 'bass_amp' => '#f97316', 'drum_kit' => '#8b5cf6',
                'keyboard' => '#3b82f6', 'monitor_wedge' => '#22c55e', 'mic_stand' => '#a855f7',
                'di_box' => '#64748b', 'vocal_mic' => '#ec4899', 'power_strip' => '#eab308',
                'riser' => '#78716c', 'custom' => '#6b7280',
            ];
            $labels = [
                'guitar_amp' => 'AMP', 'bass_amp' => 'BASS', 'drum_kit' => 'DRUMS',
                'keyboard' => 'KEYS', 'monitor_wedge' => 'MON', 'mic_stand' => 'MIC',
                'di_box' => 'DI', 'vocal_mic' => 'VOX', 'power_strip' => 'PWR',
                'riser' => 'RISER', 'custom' => '',
            ];
        @endphp
        <div class="section" style="page-break-inside: avoid;">
            <div class="section-title">Plan de scene</div>
            <div style="position: relative; width: {{ $canvasWidthPx }}px; height: {{ $canvasHeightPx }}px; background: #f0f0f0; border: 2px solid #333; margin: 10px auto;">
                {{-- Back label --}}
                <div style="position: absolute; top: 3px; width: 100%; text-align: center; font-size: 7px; color: #aaa; text-transform: uppercase; letter-spacing: 2px;">FOND DE SCENE</div>
                {{-- Stage front line --}}
                <div style="position: absolute; bottom: {{ round($canvasHeightPx * 0.08) }}px; left: 5%; right: 5%; border-top: 1px dashed #999;"></div>
                {{-- Public label --}}
                <div style="position: absolute; bottom: 3px; width: 100%; text-align: center; font-size: 7px; color: #aaa; text-transform: uppercase; letter-spacing: 2px;">PUBLIC</div>

                @foreach ($stagePlanElements as $el)
                    @php
                        $bg = $colors[$el['type']] ?? '#6b7280';
                        $elLeft = round($el['x'] / 100 * $canvasWidthPx);
                        $elTop = round($el['y'] / 100 * $canvasHeightPx);
                        $elW = round($el['width'] / 100 * $canvasWidthPx);
                        $elH = round($el['height'] / 100 * $canvasHeightPx);
                        $shortLabel = $labels[$el['type']] ?? '';
                    @endphp
                    <div style="position: absolute; left: {{ $elLeft }}px; top: {{ $elTop }}px; width: {{ $elW }}px; height: {{ $elH }}px; background: {{ $bg }}; color: white; font-size: 7px; text-align: center; border-radius: 2px; overflow: hidden; padding-top: {{ max(1, round($elH / 2) - 6) }}px;">
                        <div style="font-size: 8px; font-weight: bold; letter-spacing: 0.5px;">{{ $shortLabel }}</div>
                        <div style="font-size: 6px; margin-top: 1px;">{{ $el['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <div style="text-align: center; font-size: 9px; color: #666; margin-top: 5px;">
                Scene : {{ $stageW / 100 }}m x {{ $stageD / 100 }}m
            </div>
        </div>
    @elseif ($stagePlan?->image)
        <div class="section" style="page-break-inside: avoid;">
            <div class="section-title">Plan de scene</div>
            <div style="text-align: center; padding: 10px 0;">
                <img src="{{ public_path('storage/' . $stagePlan->image) }}" style="max-width: 100%; max-height: 350px;">
            </div>
        </div>
    @endif

    {{-- EQUIPMENT PER MEMBER --}}
    <div class="section">
        <div class="section-title">Materiel du groupe (backline)</div>

        @foreach ($members as $member)
            @if ($member->equipment->count())
                <div class="member-block">
                    <div class="member-name">
                        {{ $member->name }}
                        <span class="member-instruments">{{ $member->instruments }}</span>
                    </div>
                    <table>
                        <tr>
                            <th style="width: 120px;">Type</th>
                            <th>Element</th>
                            <th>Notes</th>
                        </tr>
                        @foreach ($member->equipment as $item)
                            <tr>
                                <td class="category">{{ $item->category_label }}</td>
                                <td>{{ $item->name }}</td>
                                <td style="color: #666;">{{ $item->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        @endforeach
    </div>

    {{-- TECHNICAL REQUIREMENTS --}}
    <div class="section">
        <div class="section-title">Besoins techniques (a fournir par la salle)</div>

        @foreach ($members as $member)
            @if ($member->techRequirement)
                @php $req = $member->techRequirement; @endphp
                @if ($req->monitors || $req->microphones || $req->power || $req->monitoring || $req->other)
                    <div class="member-block">
                        <div class="member-name">
                            {{ $member->name }}
                            <span class="member-instruments">{{ $member->instruments }}</span>
                        </div>
                        <div class="requirements" style="padding: 5px 10px;">
                            @if ($req->monitors)
                                <div class="req-item"><span class="req-label">Retours :</span> {{ $req->monitors }}</div>
                            @endif
                            @if ($req->microphones)
                                <div class="req-item"><span class="req-label">Micros / DI :</span> {{ $req->microphones }}</div>
                            @endif
                            @if ($req->power)
                                <div class="req-item"><span class="req-label">Electricite :</span> {{ $req->power }}</div>
                            @endif
                            @if ($req->monitoring)
                                <div class="req-item"><span class="req-label">Monitoring :</span> {{ $req->monitoring }}</div>
                            @endif
                            @if ($req->other)
                                <div class="req-item"><span class="req-label">Divers :</span> {{ $req->other }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        @endforeach

        {{-- Global requirements --}}
        @if ($global['global_monitors'] || $global['global_other'])
            <div class="member-block">
                <div class="member-name">Besoins globaux</div>
                <div class="requirements" style="padding: 5px 10px;">
                    @if ($global['global_monitors'])
                        <div class="req-item"><span class="req-label">Sono / Retours :</span> {{ $global['global_monitors'] }}</div>
                    @endif
                    @if ($global['global_other'])
                        <div class="req-item"><span class="req-label">Divers :</span> {{ $global['global_other'] }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- NOTES --}}
    @if ($global['global_notes'])
        <div class="section">
            <div class="section-title">Notes complementaires</div>
            <p style="padding: 5px;">{{ $global['global_notes'] }}</p>
        </div>
    @endif

    <div class="footer">
        MAMA WITCH - Fiche Technique - Generee le {{ now()->format('d/m/Y') }} - contact@mamawitch.fr
    </div>

</body>
</html>
