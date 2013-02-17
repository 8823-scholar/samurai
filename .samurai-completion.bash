#
# bash completion support for Samurai
#

_samurai()
{
  local cur prev commands

  COMPREPLY=()

  cur="${COMP_WORDS[COMP_CWORD]}"
  prev="${COMP_WORDS[COMP_CWORD-1]}"
  commands="add-project add-application add-action add-component add-filter add-template"

  case "${prev}" in
    add-action)
      words=$( \
          for c in ` \
          find ./component/action -name '*.class.php' 2> /dev/null | \
          xargs grep 'class Action_' | \
          awk '{print $2}' | \
          awk -F 'Action_' '{print $2}' | \
          tr A-Z a-z | \
          tr - _ `
          do echo ${c}
          done
      )
      if [[ "${cur}" == -* ]]; then
        COMPREPLY=($(compgen -W '--help --usage' -- ${cur}))
      else
        COMPREPLY=($(compgen -W "${words}" -- ${cur}))
      fi
      return 0
      ;;
    add-component)
      words=$( \
          for c in ` \
          find ./component -name '*.class.php' 2> /dev/null | \
          xargs grep 'class ' | \
          awk '{print $2}' | \
          tr A-Z a-z | \
          tr - _ `
          do echo ${c}
          done
      )
      if [[ "${cur}" == -* ]]; then
        COMPREPLY=($(compgen -W '--help --usage' -- ${cur}))
      else
        COMPREPLY=($(compgen -W "${words}" -- ${cur}))
      fi
      return 0
      ;;
    add-filter)
      words=$( \
          for c in ` \
          find ./component/filter -name '*.class.php' 2> /dev/null | \
          xargs grep 'class Filter_' | \
          awk '{print $2}' | \
          awk -F 'Filter_' '{print $2}' | \
          tr A-Z a-z | \
          tr - _ `
          do echo ${c}
          done
      )
      if [[ "${cur}" == -* ]]; then
        COMPREPLY=($(compgen -W '--help --usage' -- ${cur}))
      else
        COMPREPLY=($(compgen -W "${words}" -- ${cur}))
      fi
      return 0
      ;;
    *)
      ;;
  esac

  if [[ "${cur}" == -* ]]; then
    COMPREPLY=($(compgen -W '--help --usage' -- ${cur}))
  else
    COMPREPLY=($(compgen -W "${commands}" -- ${cur}))
  fi
  return 0
}

complete -o filenames -F _samurai samurai
