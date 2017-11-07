<?php
declare(strict_types=1);

namespace MWojtowicz\GusClient;

interface Constants
{

    const URL_TEST       = 'https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc';
    const URL_PRODUCTION = 'https://wyszukiwarkaregon.stat.gov.pl/wsBIR/UslugaBIRzewnPubl.svc';

    const URL_WSDL_TEST = "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/wsdl/UslugaBIRzewnPubl.xsd";
    const URL_WSDL_PRODUCTION = "https://wyszukiwarkaregontest.stat.gov.pl/wsBIR/wsdl/UslugaBIRzewnPubl.xsd";

    const SESSIONFILE_NAME = 'gusapi.session';

    const MODE_PRODUCTION = 'PRODUCTION';
    const MODE_TEST = 'TEST';

    const STATUS_AVAILABLE = 'AVAILABLE';
    const STATUS_UNAVAILABLE = 'UNAVAILABLE';
    const STATUS_TECHNICALBREAK = 'TECHNICALBREAK';

    const SESSION_ALIVE = 'ALIVE';
    const SESSION_DEAD = 'DEAD';
}
