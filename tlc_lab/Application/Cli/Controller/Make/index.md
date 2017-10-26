{% extends '@{{app}}/layout.html.twig' %}

{% block title %}{{title}} - {{ parent() }}{% endblock %}

{% set script_controller = '{{module}}/index' %}

{% set menu = '{{module}}' %}

{% block main %}

<div class="page-header clearfix">
	<button class="btn btn-info btn-sm pull-right" id="add-navigation-btn" data-toggle="modal" data-target="#modal" data-url="{{ U('{{app}}/{{module}}/create') }}"><span class="glyphicon glyphicon-plus"></span> 添加{{title}}
	</button>
	<h1 class="pull-left">{{title}}</h1>
</div>
<form id="{{module}}-search-form" class="form-inline well well-sm" action="" method="get" novalidate>

	<div class="form-group ">
		<input type="text" name="keyword"  class="form-control" placeholder="" value="{{  app.request.query.get('keyword') }}"/>
	</div>

	<button class="btn btn-primary">搜索</button>
</form>

<div id="aticle-table-container">
	<table class="table table-hover table-striped" id="article-table">
		<thead>
		<tr>
			<th>ID</th>
			<th>操作</th>
		</tr>
		</thead>
		<tbody>
		{% if list %}
		{% for vo in list %}
		<tr>
			<td>{{ vo.id}}</td>

			<td>
				<div class="btn-group">
					<a href="#modal" data-toggle="modal" data-url="{{ U('{{app}}/{{module}}/create',{ id:vo.id }) }}" class="btn btn-default btn-sm">编辑</a>
				</div>
			</td>
		</tr>
		{% endfor %}
		{% else %}
		<tr>
			<td colspan="20">
				<div class="empty">暂无页面记录</div>
			</td>
		</tr>
		{% endif %}
		</tbody>
	</table>
</div>
{{ web_macro.paginator(paginator) }}
{% endblock %}