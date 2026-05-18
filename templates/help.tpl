<style>
.mas-ah-help-tabs { margin: 20px 0 0; border-bottom: 2px solid #ddd; }
.mas-ah-help-tabs a { display: inline-block; padding: 10px 20px; margin-right: 5px; background: #f5f5f5; border: 1px solid #ddd; border-bottom: none; text-decoration: none; color: #333; }
.mas-ah-help-tabs a.active { background: #fff; border-bottom: 2px solid #fff; margin-bottom: -2px; font-weight: bold; }
.mas-ah-help-tabs a:hover { background: #e9e9e9; }
.mas-ah-help-content { padding: 20px; border: 1px solid #ddd; border-top: none; background: #fff; }
.mas-ah-help-section { display: none; }
.mas-ah-help-section.active { display: block; }
</style>

<div class="mas-ah-help-tabs">
  <a href="#mas-ah-help-general" class="mas-ah-help-tab active" data-tab="mas-ah-help-general">{$mod->Lang('help_general')|escape:'html'}</a>
  <a href="#mas-ah-help-configuration" class="mas-ah-help-tab" data-tab="mas-ah-help-configuration">{$mod->Lang('help_configuration')|escape:'html'}</a>
  <a href="#mas-ah-help-protocols" class="mas-ah-help-tab" data-tab="mas-ah-help-protocols">{$mod->Lang('help_protocols')|escape:'html'}</a>
  <a href="#mas-ah-help-security" class="mas-ah-help-tab" data-tab="mas-ah-help-security">{$mod->Lang('help_security')|escape:'html'}</a>
  <a href="#mas-ah-help-troubleshooting" class="mas-ah-help-tab" data-tab="mas-ah-help-troubleshooting">{$mod->Lang('help_troubleshooting')|escape:'html'}</a>
</div>

<div class="mas-ah-help-content">
  <div id="mas-ah-help-general" class="mas-ah-help-section active">
    {include file='module_file_tpl:MAS_AuthHub;help_general_tab.tpl'}
  </div>

  <div id="mas-ah-help-configuration" class="mas-ah-help-section">
    {include file='module_file_tpl:MAS_AuthHub;help_configuration_tab.tpl'}
  </div>

  <div id="mas-ah-help-protocols" class="mas-ah-help-section">
    {include file='module_file_tpl:MAS_AuthHub;help_protocols_tab.tpl'}
  </div>

  <div id="mas-ah-help-security" class="mas-ah-help-section">
    {include file='module_file_tpl:MAS_AuthHub;help_security_tab.tpl'}
  </div>

  <div id="mas-ah-help-troubleshooting" class="mas-ah-help-section">
    {include file='module_file_tpl:MAS_AuthHub;help_troubleshooting_tab.tpl'}
  </div>
</div>

<script>
(function() {
  var wrap = document.querySelector('.mas-ah-help-tabs');
  if (!wrap) { return; }
  var tabs = document.querySelectorAll('.mas-ah-help-tab');
  var sections = document.querySelectorAll('.mas-ah-help-section');
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
