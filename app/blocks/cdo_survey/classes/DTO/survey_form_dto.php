<?php

namespace block_cdo_survey\DTO;

use tool_cdo_config\request\DTO\base_dto;

class survey_form_dto extends base_dto
{
    public ?string $addressOfActualResidence;
    public ?string $middleName;
    public ?string $addressOfRegistration;
    public ?string $bankDetails;
    public ?string $citizenship;
    public ?bool $disabled;
    public ?string $document_type;
    public ?string $eduFile;
    public ?string $eduFileName;
    public ?bool $nameMatchesPassport;
    public ?string $educationType;
    public ?string $education_level;
    public ?string $fullnameLE;
    public ?bool $imprisonment;
    public ?string $inGroup;
    public ?array $individual;
    public ?string $informationAboutPayer;
    public ?string $inn;
    public ?string $innFile;
    public ?string $innLE;
    public ?bool $intellectualDisabilities;
    public ?string $kpp;
    public ?string $legalAddress;
    public ?bool $limitedHealth;
    public ?string $number;
    public ?string $numberEdu;
    public ?bool $parentalLeave;
    public ?string $passportFile;
    public ?bool $personalData;
    public ?bool $returnToJob;
    public ?string $scanPassportAddress;
    public ?string $serial;
    public ?string $serialEdu;
    public ?string $sex;
    public ?string $shortnameLE;
    public ?string $snils;
    public ?string $snilsFile;
    public ?string $telephone;
    public ?string $telephoneLE;
    public ?string $whenIssued;
    public ?string $whenIssuedEdu;
    public ?string $whoIssued;
    public ?string $whoIssuedEdu;
    public ?bool $insurance;
    public ?bool $unemployed;
    public ?string $accountNumber;
    public ?string $bik;
    public ?bool $isNewPassport;
    public ?string $birthday;
    public ?string $innFileName;
    public ?string $snilsFileName;
    public ?string $passportFileName;
    public ?string $course_schedule;
    public ?string $divisionCode;
    public ?string $postalAddress;
    public ?array $postalAddressData;
    public ?array $legalAddressData;
    public ?string $individual_fullName;
    public ?string $individual_sex;
    public ?string $individual_citizenship;
    public ?string $individual_birthday;
    public ?string $individual_document_type;
    public ?string $individual_passportPhotoScan;
    public ?string $individual_passportPhotoScanName;
    public ?string $individual_serial;
    public ?string $individual_number;
    public ?string $individual_whoIssued;
    public ?string $individual_whenIssued;
    public ?string $individual_addressOfRegistration;
    public ?string $individual_addressOfActualResidence;
    public ?string $individual_telephone;
    public ?string $individual_email;
    public ?string $individual_passportRegistrationScan;
    public ?string $individual_passportRegistrationScanName;
    public ?string $individual_inn;
    public ?string $individual_innScan;
    public ?string $individual_innScanName;
    public ?string $scanPassportAddressName;
    public ?string $individual_divisionCode;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '\\block_cdo_survey\\DTO\\survey_form_dto';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->addressOfActualResidence = $data->addressOfActualResidence ?? '';
        $this->middleName = $data->middlename ?? '';
        $this->addressOfRegistration = $data->addressOfRegistration ?? '';
        $this->bankDetails = $data->bankDetails ?? '';
        $this->citizenship = $data->citizenship ?? '';
        $this->disabled = $data->disabled ?? false;
        $this->document_type = $data->document_type ?? '';
        $this->eduFile = $data->eduFile ?? '';
        $this->eduFileName = $data->eduFileName ?? '';
        $this->nameMatchesPassport = $data->nameMatchesPassport ?? false;
        $this->educationType = $data->educationType ?? '';
        $this->education_level = $data->education_level ?? '';
        $this->fullnameLE = $data->fullnameLE ?? '';
        $this->imprisonment = $data->imprisonment ?? false;
        $this->inGroup = $data->inGroup ?? '';
        $this->individual = $data->individual ?? [];
        $this->informationAboutPayer = $data->informationAboutPayer ?? '';
        $this->inn = $data->inn ?? '';
        $this->innFile = $data->innFile ?? '';
        $this->innLE = $data->innLE ?? '';
        $this->intellectualDisabilities = $data->intellectualDisabilities ?? false;
        $this->kpp = $data->kpp ?? '';
        $this->legalAddress = $data->legalAddress ?? '';
        $this->limitedHealth = $data->limitedHealth ?? false;
        $this->number = $data->number ?? '';
        $this->numberEdu = $data->numberEdu ?? '';
        $this->parentalLeave = $data->parentalLeave ?? false;
        $this->passportFile = $data->passportFile ?? '';
        $this->personalData = $data->personalData ?? false;
        $this->returnToJob = $data->returnToJob ?? false;
        $this->scanPassportAddress = $data->scanPassportAddress ?? '';
        $this->serial = $data->serial ?? '';
        $this->serialEdu = $data->serialEdu ?? '';
        $this->sex = $data->sex ?? '';
        $this->shortnameLE = $data->shortnameLE ?? '';
        $this->snils = $data->snils ?? '';
        $this->snilsFile = $data->snilsFile ?? '';
        $this->telephone = $data->telephone ?? '';
        $this->telephoneLE = $data->telephoneLE ?? '';
        $this->whenIssued = $data->whenIssued ?? '';
        $this->whenIssuedEdu = $data->whenIssuedEdu ?? '';
        $this->whoIssued = $data->whoIssued ?? '';
        $this->whoIssuedEdu = $data->whoIssuedEdu ?? '';
        $this->insurance = $data->insurance ?? false;
        $this->unemployed = $data->unemployed ?? false;
        $this->accountNumber = $data->accountNumber ?? '';
        $this->bik = $data->bik ?? '';
        $this->isNewPassport = $data->isNewPassport ?? false;
        $this->birthday = $data->birthday ?? '';
        $this->innFileName = $data->innFileName ?? '';
        $this->snilsFileName = $data->snilsFileName ?? '';
        $this->passportFileName = $data->passportFileName ?? '';
        $this->course_schedule = $data->course_schedule ?? '';
        $this->divisionCode = $data->divisionCode ?? '';
        $this->postalAddress = $data->postalAddress ?? '';
        $this->postalAddressData = $data->postalAddressData ?? [];
        $this->legalAddressData = $data->legalAddressData ?? [];
        $this->individual_fullName = $data->individual_fullName ?? '';
        $this->individual_sex = $data->individual_sex ?? '';
        $this->individual_citizenship = $data->individual_citizenship ?? '';
        $this->individual_birthday = $data->individual_birthday ?? '';
        $this->individual_document_type = $data->individual_document_type ?? '';
        $this->individual_passportPhotoScan = $data->individual_passportPhotoScan ?? '';
        $this->individual_passportPhotoScanName = $data->individual_passportPhotoScanName ?? '';
        $this->individual_serial = $data->individual_serial ?? '';
        $this->individual_number = $data->individual_number ?? '';
        $this->individual_whoIssued = $data->individual_whoIssued ?? '';
        $this->individual_whenIssued = $data->individual_whenIssued ?? '';
        $this->individual_addressOfRegistration = $data->individual_addressOfRegistration ?? '';
        $this->individual_addressOfActualResidence = $data->individual_addressOfActualResidence ?? '';
        $this->individual_telephone = $data->individual_telephone ?? '';
        $this->individual_email = $data->individual_email ?? '';
        $this->individual_passportRegistrationScan = $data->individual_passportRegistrationScan ?? '';
        $this->individual_passportRegistrationScanName = $data->individual_passportRegistrationScanName ?? '';
        $this->individual_inn = $data->individual_inn ?? '';
        $this->individual_innScan = $data->individual_innScan ?? '';
        $this->individual_innScanName = $data->individual_innScanName ?? '';
        $this->scanPassportAddressName = $data->scanPassportAddressName ?? '';
        $this->individual_divisionCode = $data->individual_divisionCode ?? '';
        $this->nameMismatchFileName = $data->nameMismatchFileName ?? '';
        $this->nameMismatchFile = $data->nameMismatchFile ?? '';
        return $this;
    }
}