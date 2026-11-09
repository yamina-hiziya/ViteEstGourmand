<?php require_once _ROOTPATH_.'/templates/header.php';
/** @var string $error */
?>

<?php if ($error) { ?>
    <div class="alert alert-danger">
        <?=$error; ?>
    </div>
<?php } ?>

<?php require_once _ROOTPATH_.'/templates/footer.php'; ?>