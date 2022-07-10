Convert blade output directives

Laravel 5 now escapes all output from both the `{{ }}` and `{{{ }}}`
Blade directives. A new `{!! !!}` directive has been introduced to
display raw, unescaped output. To prevent incorrect escaping all
instances of `{{ }}` where changed to `{!! !!}`.
