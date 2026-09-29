<?php
/** Closing shell for the buyer account pages (pairs with account-top.php). Needs: $fr. */
?>
                </main>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../../../pages/partials/eco-page-footer.php'; ?>

    <script>
    // Success flashes fade out (the old components/footer.php did this; the mc-footer partial does not)
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            setTimeout(function () { el.style.display = 'none'; }, 600);
        }, 4000);
    });
    </script>
