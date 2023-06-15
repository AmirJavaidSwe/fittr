<?php

namespace App\Enums;

enum StripeCurrency: string
{
    case AED = 'aed';
    case AFN = 'afn';
    case ALL = 'all';
    case AMD = 'amd';
    case ANG = 'ang';
    case AOA = 'aoa';
    case ARS = 'ars';
    case AUD = 'aud';
    case AWG = 'awg';
    case AZN = 'azn';
    case BAM = 'bam';
    case BBD = 'bbd';
    case BDT = 'bdt';
    case BGN = 'bgn';
    case BHD = 'bhd';
    case BIF = 'bif';
    case BMD = 'bmd';
    case BND = 'bnd';
    case BOB = 'bob';
    case BRL = 'brl';
    case BSD = 'bsd';
    case BWP = 'bwp';
    case BYN = 'byn';
    case BZD = 'bzd';
    case CAD = 'cad';
    case CDF = 'cdf';
    case CHF = 'chf';
    case CLP = 'clp';
    case CNY = 'cny';
    case COP = 'cop';
    case CRC = 'crc';
    case CVE = 'cve';
    case CZK = 'czk';
    case DJF = 'djf';
    case DKK = 'dkk';
    case DOP = 'dop';
    case DZD = 'dzd';
    case EGP = 'egp';
    case ETB = 'etb';
    case EUR = 'eur';
    case FJD = 'fjd';
    case FKP = 'fkp';
    case GBP = 'gbp';
    case GEL = 'gel';
    case GIP = 'gip';
    case GMD = 'gmd';
    case GNF = 'gnf';
    case GTQ = 'gtq';
    case GYD = 'gyd';
    case HKD = 'hkd';
    case HNL = 'hnl';
    case HTG = 'htg';
    case HUF = 'huf';
    case IDR = 'idr';
    case ILS = 'ils';
    case INR = 'inr';
    case ISK = 'isk';
    case JMD = 'jmd';
    case JOD = 'jod';
    case JPY = 'jpy';
    case KES = 'kes';
    case KGS = 'kgs';
    case KHR = 'khr';
    case KMF = 'kmf';
    case KRW = 'krw';
    case KWD = 'kwd';
    case KYD = 'kyd';
    case KZT = 'kzt';
    case LAK = 'lak';
    case LBP = 'lbp';
    case LKR = 'lkr';
    case LRD = 'lrd';
    case LSL = 'lsl';
    case MAD = 'mad';
    case MDL = 'mdl';
    case MGA = 'mga';
    case MKD = 'mkd';
    case MMK = 'mmk';
    case MNT = 'mnt';
    case MOP = 'mop';
    case MRO = 'mro';
    case MUR = 'mur';
    case MVR = 'mvr';
    case MWK = 'mwk';
    case MXN = 'mxn';
    case MYR = 'myr';
    case MZN = 'mzn';
    case NAD = 'nad';
    case NGN = 'ngn';
    case NIO = 'nio';
    case NOK = 'nok';
    case NPR = 'npr';
    case NZD = 'nzd';
    case OMR = 'omr';
    case PAB = 'pab';
    case PEN = 'pen';
    case PGK = 'pgk';
    case PHP = 'php';
    case PKR = 'pkr';
    case PLN = 'pln';
    case PYG = 'pyg';
    case QAR = 'qar';
    case RON = 'ron';
    case RSD = 'rsd';
    case RUB = 'rub';
    case RWF = 'rwf';
    case SAR = 'sar';
    case SBD = 'sbd';
    case SCR = 'scr';
    case SEK = 'sek';
    case SGD = 'sgd';
    case SHP = 'shp';
    case SLE = 'sle';
    case SOS = 'sos';
    case SRD = 'srd';
    case STD = 'std';
    case SZL = 'szl';
    case THB = 'thb';
    case TJS = 'tjs';
    case TND = 'tnd';
    case TOP = 'top';
    case TRY = 'try';
    case TTD = 'ttd';
    case TWD = 'twd';
    case TZS = 'tzs';
    case UAH = 'uah';
    case UGX = 'ugx';
    case USD = 'usd';
    case UYU = 'uyu';
    case UZS = 'uzs';
    case VND = 'vnd';
    case VUV = 'vuv';
    case WST = 'wst';
    case XAF = 'xaf';
    case XCD = 'xcd';
    case XOF = 'xof';
    case XPF = 'xpf';
    case YER = 'yer';
    case ZAR = 'zar';
    case ZMW = 'zmw';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function zeroDecimal(): array
    {
        return array(
            [
                self::BIF->name => self::BIF->value,
                self::CLP->name => self::CLP->value,
                self::DJF->name => self::DJF->value,
                self::GNF->name => self::GNF->value,
                self::JPY->name => self::JPY->value,
                self::KMF->name => self::KMF->value,
                self::KRW->name => self::KRW->value,
                self::MGA->name => self::MGA->value,
                self::PYG->name => self::PYG->value,
                self::RWF->name => self::RWF->value,
                self::UGX->name => self::UGX->value,
                self::VND->name => self::VND->value,
                self::VUV->name => self::VUV->value,
                self::XAF->name => self::XAF->value,
                self::XOF->name => self::XOF->value,
                self::XPF->name => self::XPF->value,
            ],
        );
    }

    public static function threeDecimal(): array
    {
        return array(
            [
                self::BHD->name => self::BHD->value,
                self::JOD->name => self::JOD->value,
                self::KWD->name => self::KWD->value,
                self::OMR->name => self::OMR->value,
                self::TND->name => self::TND->value,
            ],
        );
    }

    public static function minAmount(string $name = '')
    {
        $case = strtoupper($name);
        //human amount
        return match(true) {
            $case == self::USD->name => 0.5,
            $case == self::AED->name => 2,
            $case == self::AUD->name => 0.5,
            $case == self::BGN->name => 1,
            $case == self::BRL->name => 0.5,
            $case == self::CAD->name => 0.5,
            $case == self::CHF->name => 0.5,
            $case == self::CZK->name => 15,
            $case == self::DKK->name => 2.5,
            $case == self::EUR->name => 0.5,
            $case == self::GBP->name => 0.3,
            $case == self::HKD->name => 4,
            $case == self::HUF->name => 175,
            $case == self::INR->name => 0.5,
            $case == self::JPY->name => 50,
            $case == self::MXN->name => 10,
            $case == self::MYR->name => 2,
            $case == self::NOK->name => 3,
            $case == self::NZD->name => 0.5,
            $case == self::PLN->name => 2,
            $case == self::RON->name => 2,
            $case == self::SEK->name => 3,
            $case == self::SGD->name => 0.5,
            $case == self::THB->name => 10,
            default => 0,
        };
    }
}
