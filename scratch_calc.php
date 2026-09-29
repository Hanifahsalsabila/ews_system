<?php
function calcZ($val, $l, $m, $s) {
    if (abs($l) < 0.0001) return log($val / $m) / $s;
    return (pow(($val / $m), $l) - 1.0) / ($l * $s);
}
function calcExt($val, $l, $m, $s, $z) {
    if ($z > 3) {
        $sd2pos = $m * pow((1 + $l * $s * 2), (1 / $l));
        $sd3pos = $m * pow((1 + $l * $s * 3), (1 / $l));
        return 3 + ($val - $sd3pos) / ($sd3pos - $sd2pos);
    }
    if ($z < -3) {
        $sd2neg = $m * pow((1 + $l * $s * -2), (1 / $l));
        $sd3neg = $m * pow((1 + $l * $s * -3), (1 / $l));
        return -3 + ($val - $sd3neg) / ($sd2neg - $sd3neg);
    }
    return $z;
}

// 4. WFL/WFH INTERPOLATION
// WFL 68.25
$l1 = -0.3521; $m1 = 8.0132; $s1 = 0.08214;
$l2 = -0.3521; $m2 = 8.0360; $s2 = 0.08215;
$alpha = 0.5;
$l_int = $l1 + $alpha * ($l2 - $l1);
$m_int = $m1 + $alpha * ($m2 - $m1);
$s_int = $s1 + $alpha * ($s2 - $s1);
$wfl_z = calcZ(8.1, $l_int, $m_int, $s_int);

echo "WFL 68.25: L=$l_int, M=$m_int, S=$s_int, Z=$wfl_z\n";

// WFH 90.25
$hl1 = -0.3521; $hm1 = 12.9334; $hs1 = 0.0803;
$hl2 = -0.3521; $hm2 = 12.9569; $hs2 = 0.0803;
$hl_int = $hl1 + $alpha * ($hl2 - $hl1);
$hm_int = $hm1 + $alpha * ($hm2 - $hm1);
$hs_int = $hs1 + $alpha * ($hs2 - $hs1);
$wfh_z = calcZ(13.0, $hl_int, $hm_int, $hs_int);

echo "WFH 90.25: L=$hl_int, M=$hm_int, S=$hs_int, Z=$wfh_z\n";

// 5. WAZ / HAZ INDEPENDENT CHECK
$waz0_z = calcZ(3.3, 0.3487, 3.3464, 0.14602);
echo "WAZ 0: Z=$waz0_z\n";

$waz731_z = calcZ(12.2, -0.0137, 12.1548, 0.11427);
echo "WAZ 731: Z=$waz731_z\n";

$haz731_z = calcZ(86.4, 1.0, 87.1303, 0.03508);
echo "HAZ 731: Z=$haz731_z\n";

// 6. EXTREME-Z INDEPENDENT CHECK
// WAZ 365, weight 15.0
$waz365_15_z = calcZ(15.0, 0.0645, 9.646, 0.10925);
$waz365_15_ext = calcExt(15.0, 0.0645, 9.646, 0.10925, $waz365_15_z);
echo "WAZ 365 15.0: Z=$waz365_15_z, Ext=$waz365_15_ext\n";

// WAZ 365, weight 4.0
$waz365_4_z = calcZ(4.0, 0.0645, 9.646, 0.10925);
$waz365_4_ext = calcExt(4.0, 0.0645, 9.646, 0.10925, $waz365_4_z);
echo "WAZ 365 4.0: Z=$waz365_4_z, Ext=$waz365_4_ext\n";
