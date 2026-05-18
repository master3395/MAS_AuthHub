<style>
.mas-ah-about-tabs { margin: 20px 0 0; border-bottom: 2px solid #ddd; }
.mas-ah-about-tabs a { display: inline-block; padding: 10px 20px; margin-right: 5px; background: #f5f5f5; border: 1px solid #ddd; border-bottom: none; text-decoration: none; color: #333; }
.mas-ah-about-tabs a.active { background: #fff; border-bottom: 2px solid #fff; margin-bottom: -2px; font-weight: bold; }
.mas-ah-about-tabs a:hover { background: #e9e9e9; }
.mas-ah-about-content { padding: 20px; border: 1px solid #ddd; border-top: none; background: #fff; }
.mas-ah-about-section { display: none; }
.mas-ah-about-section.active { display: block; }
</style>

<div class="mas-ah-about-tabs">
  <a href="#mas-ah-about-module" class="mas-ah-about-tab active" data-tab="mas-ah-about-module">{$mod->Lang('about_tab_module')|escape:'html'}</a>
  <a href="#mas-ah-about-compat" class="mas-ah-about-tab" data-tab="mas-ah-about-compat">{$mod->Lang('about_tab_compat')|escape:'html'}</a>
  <a href="#mas-ah-about-summary" class="mas-ah-about-tab" data-tab="mas-ah-about-summary">{$mod->Lang('about_tab_summary')|escape:'html'}</a>
  <a href="#mas-ah-about-changelog" class="mas-ah-about-tab" data-tab="mas-ah-about-changelog">{$mod->Lang('about_tab_changelog')|escape:'html'}</a>
</div>

<div class="mas-ah-about-content">
  <motion id="mas-ah-about-module" class="mas-ah-about-section active">
    {include file='module_file_tpl:MAS_AuthHub;about_module_tab.tpl'}
  </motion>

  <motion id="mas-ah-about-compat" class="mas-ah-about-section">
    {include file='module_file_tpl:MAS_AuthHub;about_compat_tab.tpl'}
  </motion>

  <motion id="mas-ah-about-summary" class="mas-ah-about-section">
    {include file='module_file_tpl:MAS_AuthHub;about_summary_tab.tpl'}
  </motion>

  <motion id="mas-ah-about-changelog" class="mas-ah-about-section">
    {include file='module_file_tpl:MAS_AuthHub;about_changelog_tab.tpl'}
  </motion>
</motion>

<script>
(function() {
  var wrap = document.querySelector('.mas-ah-about-tabs');
  if (!wrap) { return; }
  var tabs = document.querySelectorAll('.mas-ah-about-tab');
  var sections = document.querySelectorAll('.mas-ah-about-section');
  tabs.forEach(function(tab) {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      var target = this.getAttribute('data-tab');
      tabs.forEach(function(t) { t.classList.remove('active'); });
      sections.forEach(function(s) { s.classList.remove('active'); });
      this.classList.add('active');
      var panel = document.getElementById(target);
      if (panel) { panel.classList.add('active'); }
    });
  });
})();
</script>
