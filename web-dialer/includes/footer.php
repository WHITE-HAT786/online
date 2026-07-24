      </main>

      <footer class="page-footer">
        © <?= date('Y') ?> WebDialer. All rights reserved.
      </footer>
    </div><!-- /.main -->
  </div><!-- /.app -->

  <?php include __DIR__ . '/modal.php'; ?>

  <script src="../assets/js/app.js?v=<?= @filemtime(__DIR__ . '/../assets/js/app.js') ?: time() ?>"></script>
</body>
</html>
