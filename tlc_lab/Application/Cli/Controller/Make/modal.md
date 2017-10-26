{% extends '@Home/bootstrap-modal-layout.html.twig' %}

{% set {{module}} = {{module}}|default(null) %}


{% block title %}
{% if school %}编辑{{title}}{% else %}添加{{title}}{% endif %}
{% endblock %}

{% block body %}

<form class="form-horizontal" id="{{module}}-form"
      {% if {{module}}.id == 0 %}
      action="{{ U('{{app}}/{{module}}/create') }}"
      {% else %}
      action="{{ U('{{app}}/{{module}}/update') }}"
      {% endif %}
      method="post">

	<div class="row form-group">
		<div class="col-md-3 control-label"><label for="name">example</label></div>
		<div class="col-md-8 controls">
			<input class="form-control" type="text" id="example" value="" name="example" placeholder=""></div>
	</div>

	<input type="hidden" name="id" value="{{ {{module}}.id|default(0) }}">
</form>

<script type="text/javascript">
	app.load('{{module}}/create-modal')
</script>

{% endblock %}
{% block footer %}
<button type="button" class="btn btn-link" data-dismiss="modal" id="cancel-btn">取消</button>
<button id="{{module}}-save-btn" data-submiting-text="正在提交" type="submit" class="btn btn-primary" data-toggle="form-submit" data-target="#{{module}}-form">保存</button>
{% endblock %}

{% set hideFooter = true %}