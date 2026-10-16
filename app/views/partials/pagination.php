<?php if (!empty($prevUrl) || !empty($nextUrl)): ?>
<nav class="pager">
  <?php if (!empty($prevUrl)): ?><a class="btn ghost" href="<?= e($prevUrl) ?>"><i class="bi bi-chevron-left"></i> Newer</a><?php else: ?><span></span><?php endif; ?>
  <?php if (!empty($nextUrl)): ?><a class="btn ghost" href="<?= e($nextUrl) ?>">Older <i class="bi bi-chevron-right"></i></a><?php endif; ?>
</nav>
<?php endif; ?>
