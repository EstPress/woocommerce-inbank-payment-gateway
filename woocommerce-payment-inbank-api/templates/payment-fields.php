<label><?= __('Nimi', 'woocommerce-payment-inbank-api'); ?><input type="text" name="fullname"></label>
<label><?= __('Isikukood', 'woocommerce-payment-inbank-api'); ?><input type="text" name="idcode"></label>
<label><?= __('Dokumendi tüüp', 'woocommerce-payment-inbank-api'); ?>
    <select name="doctype">
        <option value=""><?= __('(Vali tüüp)', 'woocommerce-payment-inbank-api'); ?></option>
        <?php foreach($sorted_options['doctype'] as $doctype): ?>
            <option value="<?= $doctype->option->value; ?>"><?= $doctype->option->name; ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label><?= __('Dokumendi nr.', 'woocommerce-payment-inbank-api'); ?><input type="text" name="document_nr"></label>
<label><?= __('Telefon', 'woocommerce-payment-inbank-api'); ?><input type="tel" name="phone"></label>
<label><?= __('E-mail', 'woocommerce-payment-inbank-api'); ?><input type="email" name="email"></label>
<label><?= __('Aadress', 'woocommerce-payment-inbank-api'); ?> <?= __('(Tänav, Maja/Korter)', 'woocommerce-payment-inbank-api'); ?><input type="text" name="address"></label>
<label><?= __('Linn', 'woocommerce-payment-inbank-api'); ?> <input type="text" name="city"></label>
<label><?= __('Maakond', 'woocommerce-payment-inbank-api'); ?>
    <select name="county">
        <option value=""><?= __('(Vali maakond)', 'woocommerce-payment-inbank-api'); ?></option>
        <?php foreach($sorted_options['county'] as $doctype): ?>
            <option value="<?= $doctype->option->value; ?>"><?= $doctype->option->name; ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label><?= __('Postiindeks', 'woocommerce-payment-inbank-api'); ?> <input type="text" name="zip"></label>
<label>
    <?= __('Suhtluskeel', 'woocommerce-payment-inbank-api'); ?>
    <select name="language">
        <option value=""><?= __('(Vali suhtluskeel)', 'woocommerce-payment-inbank-api'); ?></option>
        <?php foreach($sorted_options['language'] as $doctype): ?>
            <option value="<?= $doctype->option->value; ?>"><?= $doctype->option->name; ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label><?= __('Amet', 'woocommerce-payment-inbank-api'); ?> <input type="text" name="job" ></label>
<label><?= __('Töökoht', 'woocommerce-payment-inbank-api'); ?> <input type="text" name="employee" ></label>
<label><?= __('Sissetulek', 'woocommerce-payment-inbank-api'); ?> <input type="number" name="wage" ></label>
<label><?= __('Olemasolevad kohustused', 'woocommerce-payment-inbank-api'); ?> <input type="number" name="expenses"></label>
<label>
    <?= __('Pank', 'woocommerce-payment-inbank-api'); ?>
    <select name="bank">
        <option value=""><?= __('(Vali pank)', 'woocommerce-payment-inbank-api'); ?></option>
        <?php foreach($sorted_options['bank'] as $doctype): ?>
            <option value="<?= $doctype->option->value; ?>"><?= $doctype->option->name; ?></option>
        <?php endforeach; ?>
    </select>
</label>
<label><?= __('Konto nr. / IBAN', 'woocommerce-payment-inbank-api'); ?> <input type="text" name="account_nr"></label>
<label>
    <?php
        _e('Osamaksete arv', 'woocommerce-payment-inbank-api');
        $label = __('kuud', 'woocommerce-payment-inbank-api');
    ?>
    <select name="payments">
        <option value=""><?= __('(Vali järelmaksu pikkus)', 'woocommerce-payment-inbank-api'); ?></option>
        <option value="48">48 <?= $label; ?></option>
        <option value="42">42 <?= $label; ?></option>
        <option value="36">36 <?= $label; ?></option>
        <option value="30">30 <?= $label; ?></option>
        <option value="24">24 <?= $label; ?></option>
        <option value="18">18 <?= $label; ?></option>
        <option value="12">12 <?= $label; ?></option>
        <option value="6">6 <?= $label; ?></option>
    </select>
</label>
<label><?= __('Sissemaks', 'woocommerce-payment-inbank-api'); ?><input type="number" name="down_payment"></label>
<label>
    <input type="checkbox" name="inbank_tos" value="yes">
    <?= __('Olen teadlik ja nõustun, et osamaksetega tasumise müügilepingu sõlmimise otsustamiseks
    edastatakse käesolevas sooviavalduses toodud minu andmed Inbank AS-ile (registrikood 12001988). Kinnitan, et kõik esitatud andmed on õiged, täielikud ja kajastavad minu finantsolukorda sooviavalduse esitamise hetkel ning on AS Inbank nõudel dokumentaalselt tõendatavad. Olen teadlik, et ebaõigete andmete esitamisel võib Inbank AS minu sooviavalduse tagasi lükata või sooviavalduse põhjal minuga sõlmitud lepingu ennetähtaegselt üles öelda. Volitan AS-i Inbank teenuse osutamise analüüsi teostamise eesmärgil nõudma ja saama minu kohta täiendavaid andmeid kolmandatelt isikutelt. Olen teadlik, et AS Inbank võib teha mulle krediidi väljastamiseks automaatse otsuse minu poolt taotluses esitatud ja muudes asjakohases andmekogudes oleva andmete põhjal ning mul on tehtava otsuse kohta võimalik esitada vastuväide oma õigustatud huvi kaitseks. Annan AS-ile Inbank nõusoleku oma isikuandmete töötlemiseks vastavalt AS-i Inbank Kliendiandmete Töötlemise Põhimõtetele, mis on kättesaadavad veebilehel www.inbank.ee.
    Olen nõus, et taotletava krediidi ja käesoleva sooviavalduse alusel tehtud pakkumise andmeid edastatakse AS Inbank konsolideerimisgrupi- ja/või sidusettevõtetele vastutustundliku laenamise põhimõtte täitmise eesmärgil.', 'woocommerce-payment-inbank-api'); ?>
</label>
