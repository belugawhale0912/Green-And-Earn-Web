<?php if(isset($_SESSION['popupMessage'])):?>
    <script>
        showMessage(
            "<?php echo $_SESSION['popupMessage']['title']?>",
            "<?php echo $_SESSION['popupMessage']['text']?>",
            "<?php echo $_SESSION['popupMessage']['icon']?>",
        );
    </script>
    <?php unset($_SESSION['popupMessage'])?>
<?php endif;?>